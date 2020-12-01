/**
 * Send the details of the update request through
 * to /allocations/update
 */
var updateAllocation = function (e) {
    var allocation = {
        'id': $(this).data('id'),
        'allocation_type': $(this).data('type'),
        'amount': $(this).val(),
        'allocation_date': $(this).data('date'),
        '_token': $('meta[name="csrf-token"]').attr('content')
    };

    // console.table([allocation]);

    $.post(
        '/allocations/update',
        allocation
    ).done( function (data) {
        // console.log(data);
    });
};

/**
 *  Cascade through all account flows and calculate the account total
 */
var calculateAccountTotal = function (e) {
    let accountId = $(this).data('parent');
    let date = $(this).data('date');
    let total = 0;

    let flows = $('.flow input[data-parent='+accountId+'][data-date='+date+']');

    flows.each( function () {
        let value = parseInt($(this).val());
        if(!value)
        {
            value = 0;
        }

        if ($(this).data('direction') == 'negative')
        {
            total = total - value;
        }
        else
        {
            total = total + value;
        }
    });

    let accountInput = $('.account-value[data-id="'+ accountId +'"][data-date='+date+']');
    accountInput.val(total);
    accountInput.trigger('change');
    // return total;
}

var calculateProjectedTotal = function (e) {

    let hierarchy = $(this).data('hierarchy');
    let date = $(this).data('date');
    let percentage = parseFloat( $(this).parent().data('percentage') / 100 ) ?? 0;

    // sum all the values from revenue account (should be 1 account...)
    let revenue = 0;
    let salestax = calculateHierarchyValueOnDate(date, 'salestax');
    let prereal = calculateHierarchyValueOnDate(date, 'prereal');
    let postreal = calculateHierarchyValueOnDate(date, 'postreal');
    let pretotal = calculateHierarchyValueOnDate(date, 'pretotal');

    let revenueOnDate = $(`.account-value[data-hierarchy='revenue'][data-date='${date}']`);

    revenueOnDate.each( function () {
        revenue = parseInt(revenue) + parseInt($(this).val());
    });

    let receiptsToAllocate = parseInt(revenue + pretotal);
    // percentage is passed as zero on no sales tax accounts - figure out how to keep date specific salestax percentage
    let salestaxPercentage = $(`.account[data-hierarchy='salestax'][data-date='${date}']`).data('percentage');
    let netCashReceipts = parseInt(receiptsToAllocate / ((salestaxPercentage / 100) + 1));
    let realRevenue = parseInt(netCashReceipts) + parseInt(prereal);
    let projectedTotalField = $(this).parent().find(`.projected-total`);
    let placeholderValue = revenue;

    switch(hierarchy) {
        case 'revenue':
            placeholderValue = parseInt(getAdjustedDailyAccountTotal(projectedTotalField) + getPreviousProjectedTotal(projectedTotalField));
            break;
        case 'pretotal':
            placeholderValue = calculatePretotalPlaceholder(projectedTotalField);
            break;
        case 'salestax':
            placeholderValue = parseInt(receiptsToAllocate - netCashReceipts);
            break;
        case 'prereal':
            placeholderValue = parseInt(netCashReceipts * percentage);
            break;
        case 'postreal':
            placeholderValue = parseInt(realRevenue * percentage);
            break;
    }

    // placeholderValue = parseInt(placeholderValue) + getPreviousProjectedTotal(projectedTotalField);
    // let placeholderValue = parseInt(getAdjustedDailyAccountTotal(projectedTotalField)) + getPreviousProjectedTotal(projectedTotalField);

    projectedTotalField.attr('placeholder', placeholderValue);

}

function calculateHierarchyValueOnDate(date, hierarchy) {
    let selector = $(`.account-value[data-hierarchy='${hierarchy}'][data-date='${date}']`);
    let value = 0;

    selector.each( function () {
        let valueOnDate = $(this).parent().find('.projected-total').attr('placeholder');
        value = parseInt(value) + parseInt(valueOnDate);
    });

    return value;
}

function calculatePretotalPlaceholder(projectedTotalField)
{

    let dayTotal = getAdjustedDailyAccountTotal(projectedTotalField);
    let previousProjected = getPreviousProjectedTotal(projectedTotalField);

    return parseInt( dayTotal + previousProjected );

}

function getPreviousProjectedTotal(currentProjectedTotalField) {
    // get the col id from the passed projected total input
    let col = currentProjectedTotalField.parent().data('col');
    let row = currentProjectedTotalField.parent().data('row');

    // if this is the first entry, return 0
    if (col === 1) {
        return parseInt(0);
    }

    // locate the previous column
    col = parseInt(col) - 1;
    let previousProjectedTotalField = $(`.account[data-col='${col}'][data-row='${row}'] .projected-total`);

    let previousProjectedTotal = previousProjectedTotalField.attr('placeholder');

    if(previousProjectedTotalField.val()) {
        previousProjectedTotal = parseInt(previousProjectedTotalField.val());
    }
    return parseInt(previousProjectedTotal);

}

function getAdjustedDailyAccountTotal(currentProjectedTotalField) {
    let adjustedAccountTotalField = currentProjectedTotalField.parent().find(`.account-value`);

    return parseInt(adjustedAccountTotalField.val());
}

function setCumulativeTotal(targetField) {

    let row = targetField.parent().data('row');
    let col = targetField.parent().data('col');

    let value = 0;

    // if this is not the first column, get the previous cumulative total
    if (col > 1) {
        let previousTotalField = $(`.account[data-col="${col - 1}"][data-row='${row}'] .cumulative`).first();
        let previousTotal = parseInt(previousTotalField.val());

        value = value + parseInt(previousTotal);

    }

    // get the adjusted day total
    let accountRow = $(`.account[data-col='${col}'][data-row='${row}']`).first();
    let accountValueField = accountRow.find(`.account-value`).first();
    let projectedTotalField = accountRow.find(`.projected-total`).first();
    let adjTotal = parseInt(accountValueField.val()) + parseInt(projectedTotalField.attr('placeholder'));

    // revenue accounts do not accumulate projected total
    if (accountRow.data('hierarchy') == 'revenue') {
        adjTotal = parseInt(accountValueField.val());
    }

    value = value + adjTotal;


    targetField.val(parseInt(value));

}

// upon changing the value of a flow input, update the Allocation in the DB
$('.allocation-value').on("change", updateAllocation);
// $('.allocation-value').on("change", setCumulativeTotal( $(this).parent().find('.cumulative') ));
// if an AccountFlow is updated, calculate the new BankAccount total
$('.flow .allocation-value').on("change", calculateAccountTotal);
// calculate projected values
$.each($('.account .allocation-value'), calculateProjectedTotal);
$('.account .allocation-value').on("change", $.each($('.account .allocation-value'), calculateProjectedTotal));
$('.cumulative').each( function() {
    setCumulativeTotal( $(this) );
 });

