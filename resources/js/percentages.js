/**
 * Send the details of the update request through
 * to /percentages/update
 */
var updateAllocationPercentage = function (e) {
    var percentage = {
        'phase_id': $(this).data('phase-id'),
        'bank_account_id': $(this).data('account-id'),
        'percent': $(this).val(),
        '_token': $('meta[name="csrf-token"]').attr('content')
    };

    console.table([percentage]);

    $.post(
        '/percentages/update',
        percentage
    ).done( function (data) {
        console.log(data);
    });
};

// upon changing the value of a flow input, update the AllocationPercentage in the DB
$('.percentage-value').on("change", updateAllocationPercentage);
