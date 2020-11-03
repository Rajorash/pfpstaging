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

$('.allocation-value').on("change", updateAllocation);
