// update allocations on input change
var updateAllocation = function (e) {
    var allocation = {
        'id': $(this).data('id'),
        'type': $(this).data('type'),
        'value': $(this).val(),
        '_token': $('meta[name="csrf-token"]').attr('content')
    };
    
    console.table([allocation]);

    $.post(
        '/allocations/update',
        allocation
    ).done(
        alert('done')
    );
};

$('.allocation-value').on("change", updateAllocation);