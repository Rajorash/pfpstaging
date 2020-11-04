// update allocations on input change
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

$('.allocation-value').on("change", updateAllocation);
// $('.flow .allocation-value').on("change", function (e) {
//     let total = $(this).calculateAccountTotal;
//     let accountId = $(this).data('parent');
//     alert(total);

//     $('.account-value[data-id="'+ accountId +'"]').val(total);
// });
$('.flow .allocation-value').on("change", calculateAccountTotal);
