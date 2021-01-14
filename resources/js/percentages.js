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

    // console.table([percentage]);

    $.post(
        '/percentages/update',
        percentage
    ).done( function (data) {
        console.log(data);
    });
};

// calculate the current total percentage value of each phase
var updatePercentageTotal = function (e) {
    let phase_id = $(this).data('phase-id');
    console.log(`Function ${phase_id}`);
    let percentageTotalField = $(`.percentage-total[data-phase-id='${phase_id}']`);

    let total = calculatePhaseTotal(phase_id);

    if(total > 100) {
        percentageTotalField.addClass('text-danger');
    } else {
        percentageTotalField.removeClass('text-danger');
    }

    percentageTotalField.text( `${total}%` );

}

function calculatePhaseTotal(phase_id) {
    let phasePercentagesFields = $(`.percentage-value[data-phase-id='${phase_id}']`);
    let total = 0;

    phasePercentagesFields.each( function() {
        let value = parseFloat($(this).val());

        if (!isNaN(value)) {
            total = total + value;
        }
    });

    return total;

}

// allow arrow navigation of table
$('#percentagesTable').arrowTable();
// set initial values
$.each( $('.percentage-total'), updatePercentageTotal );

// upon changing the value of a flow input, update the AllocationPercentage in the DB
$('.percentage-value').on( "change", updateAllocationPercentage );
// upon changing the value of a flow input, update the total value below
$('.percentage-value').on( "change", updatePercentageTotal );
