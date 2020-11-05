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

    console.table([allocation]);

    $.post(
        '/allocations/update',
        allocation
    ).done( function (data) {
        console.log(data);
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

    flows.each( function (flow) {
        let value = parseFloat($(this).val());
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

// upon changing the value of a flow input, update the Allocation in the DB
$('.allocation-value').on("change", updateAllocation);
// if an AccountFlow is updated, calculate the new BankAccount total
$('.flow .allocation-value').on("change", calculateAccountTotal);
