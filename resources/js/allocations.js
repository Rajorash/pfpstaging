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

    let accountInput = $('.daily-total[data-id="'+ accountId +'"][data-date='+date+']');
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

    let revenueOnDate = $(`.daily-total[data-hierarchy='revenue'][data-date='${date}']`);

    revenueOnDate.each( function () {
        revenue = parseInt(revenue) + parseInt($(this).val());
    });

    let receiptsToAllocate = parseInt(revenue + pretotal);
    // percentage is passed as zero on no sales tax accounts - figure out how to keep date specific salestax percentage
    let salestaxPercentage = $(`.account[data-hierarchy='salestax'][data-date='${date}']`).data('percentage');
    let salestaxDivisor = (salestaxPercentage / 100) + 1;
    let netCashReceipts = Math.round(receiptsToAllocate / salestaxDivisor);
    let realRevenue = parseInt(netCashReceipts) - parseInt(prereal);
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
           placeholderValue = parseInt(receiptsToAllocate) - parseInt(netCashReceipts);
            break;
        case 'prereal':
            placeholderValue = parseInt(netCashReceipts * percentage);
            break;
        case 'postreal':
            placeholderValue = parseInt(realRevenue * percentage);
            break;
    }

    projectedTotalField.attr('placeholder', placeholderValue);

}

function calculateHierarchyValueOnDate(date, hierarchy) {
    let selector = $(`.daily-total[data-hierarchy='${hierarchy}'][data-date='${date}']`);
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

    return parseInt( dayTotal );

}

function getPreviousProjectedTotal(currentProjectedTotalField) {
    // get the col id from the passed projected total input
    let col = currentProjectedTotalField.parent().data('col');
    let row = currentProjectedTotalField.parent().data('row');

    // if this is the first entry, return 0
    if (col === 1) {
        return 0;
    }

    // locate the previous column
    col = col - 1;
    let previousProjectedTotalField = $(`.account[data-col='${col}'][data-row='${row}'] .projected-total`);

    return parseInt( getAccountValue( previousProjectedTotalField ) );

}

function getAdjustedDailyAccountTotal(currentProjectedTotalField) {
    let adjustedAccountTotalField = currentProjectedTotalField.parent().find(`.daily-total`);

    return parseInt(adjustedAccountTotalField.val());
}

function setCumulativeTotal(targetField) {

    let row = targetField.parent().data('row');
    let col = targetField.parent().data('col');

    let value = 0;

    // if this is not the first column, get the previous cumulative total
    if (col > 1) {
        let previousTotalField = $(`.account[data-col="${col - 1}"][data-row='${row}'] .cumulative`).first();
        let previousTotal = parseInt(previousTotalField.attr('placeholder'));

        // if a value has been entered in the previous total, override the placeholder calculation.
        if (previousTotalField.val()) {
            previousTotal = parseInt(previousTotalField.val());
        }

        value = value + parseInt(previousTotal);

    }

    // get the adjusted day total
    let accountRow = $(`.account[data-col='${col}'][data-row='${row}']`).first();
    let accountValueField = accountRow.find(`.daily-total`).first();
    let projectedTotalField = accountRow.find(`.projected-total`).first();
    let adjTotal = parseInt(accountValueField.val()) + parseInt(projectedTotalField.attr('placeholder'));

    // revenue accounts do not accumulate projected total
    if (accountRow.data('hierarchy') == 'revenue') {
        adjTotal = parseInt(accountValueField.val());
    }
    // pretotal accounts do not accumulate projected total
    if (accountRow.data('hierarchy') == 'pretotal') {
        adjTotal = parseInt(accountValueField.val());
    }

    value = value + adjTotal;


    // targetField.attr('placeholder', parseInt(value));
    targetField.val(parseInt(value));

}

function getAccountValue(element) {
    return element.val() ?? element.attr('placeholder');
}

// allow arrow navigation of table
$('#allocationTable').arrowTable({
    focusTarget: 'input:enabled'
});
// upon changing the value of a flow input, update the Allocation in the DB
$('.allocation-value, .daily-total[data-hierarchy="revenue"]').on("change", updateAllocation);
// if an AccountFlow is updated, calculate the new BankAccount total
$('.flow .allocation-value').on("change", calculateAccountTotal);
// if an account allocation changes, update all account allocations
$('.account .daily-total').on("change", $.each($('.account .daily-total'), calculateProjectedTotal));

// calculate projected values
$.each($('.account .daily-total'), calculateProjectedTotal);
// calculate cumulative totals based on previous and current values for each date
$('.cumulative').each( function() {
    setCumulativeTotal( $(this) );
});

// if anything in the table changes, roll all calculations again to update the values
$('#allocationTable').on('change', function() {
    $.each($('.account .daily-total'), calculateProjectedTotal);
    $('.cumulative').each( function() {
        setCumulativeTotal( $(this) );
    });
});

