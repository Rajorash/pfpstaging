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
    var revenue = 0;
    var salestax = 0;
    var prereal = 0;
    var postreal = 0;
    var pretotal = 0;

    let revenueOnDate = $(`.account-value[data-hierarchy='revenue'][data-date='${date}']`);

    revenueOnDate.each( function () {
        revenue = parseFloat(revenue) + parseFloat($(this).val());
    });

    let pretotalOnDate = $(`.account-value[data-hierarchy='pretotal'][data-date='${date}']`);

    pretotalOnDate.each( function () {
        pretotal = parseFloat(pretotal) + parseFloat($(this).val());
    });

    let salestaxOnDate = $(`.account-value[data-hierarchy='salestax'][data-date='${date}']`);

    salestaxOnDate.each( function () {
        salestax = parseFloat(salestax) + parseFloat($(this).val());
    });

    let prerealOnDate = $(`.account-value[data-hierarchy='prereal'][data-date='${date}']`);

    prerealOnDate.each( function () {
        prereal = parseFloat(prereal) + parseFloat($(this).val());
    });

    let postrealOnDate = $(`.account-value[data-hierarchy='postreal'][data-date='${date}']`);

    postrealOnDate.each( function () {
        postreal = parseFloat(postreal) + parseFloat($(this).val());
    });

    let receiptsToAllocate = parseFloat(revenue) + parseFloat(pretotal);
    let netCashReceipts = receiptsToAllocate / (percentage + 1);
    let realRevenue = netCashReceipts + parseFloat(prereal);

    // if (hierarchy == 'salestax')
    // {

    //     console.table([
    //         ["revenue",revenue],
    //         ["pretotal",pretotal],
    //         ["receiptsToAllocate",receiptsToAllocate],
    //         ["salestax",salestax],
    //         ["netCashReceipts",netCashReceipts],
    //         ["prereal",prereal],
    //         ["realRevenue",realRevenue],
    //         ["postreal",postreal],
    //         ["percentage",percentage],
    //         ["hierarchy",hierarchy]

    //     ]);
    // }


    let projectedTotalField = $(this).parent().find(`.projected-total`);
    let placeholderValue = revenue;

    switch(hierarchy) {
        case 'revenue':
            placeholderValue = parseInt(getAdjustedDailyAccountTotal(projectedTotalField)) + getPreviousProjectedTotal(projectedTotalField);
            break;
        case 'pretotal':
            placeholderValue = parseInt(getAdjustedDailyAccountTotal(projectedTotalField)) + getPreviousProjectedTotal(projectedTotalField);
            break;
        case 'salestax':
            placeholderValue = Math.ceil(receiptsToAllocate - netCashReceipts);
            break;
        case 'prereal':
            placeholderValue = Math.ceil(netCashReceipts * percentage);
            break;
        case 'postreal':
            placeholderValue = Math.ceil(realRevenue * percentage);
            break;
    }





    // placeholderValue = parseInt(placeholderValue) + getPreviousProjectedTotal(projectedTotalField);
    // let placeholderValue = parseInt(getAdjustedDailyAccountTotal(projectedTotalField)) + getPreviousProjectedTotal(projectedTotalField);

    projectedTotalField.attr('placeholder', placeholderValue);

    // console.table([
    //     ["revenue",revenue],
    //     ["pretotal",pretotal],
    //     ["receiptsToAllocate",receiptsToAllocate],
    //     ["salestax",salestax],
    //     ["netCashReceipts",netCashReceipts],
    //     ["prereal",prereal],
    //     ["realRevenue",realRevenue],
    //     ["postreal",postreal]
    // ]);
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


// upon changing the value of a flow input, update the Allocation in the DB
$('.allocation-value').on("change", updateAllocation);
// if an AccountFlow is updated, calculate the new BankAccount total
$('.flow .allocation-value').on("change", calculateAccountTotal);
// calculate projected values
$.each($('.account .allocation-value'), calculateProjectedTotal);
$('.account .allocation-value').on("change", calculateProjectedTotal);
