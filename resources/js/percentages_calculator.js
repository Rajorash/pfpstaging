import {calculatorCore} from "./calculator_core";

$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class PercentagesCalculator extends calculatorCore {
        constructor() {
            super();

            this.ajaxUrl = window.percentagesControllerUpdate;
            this.elementTablePlace = $('#percentagesTablePlace');
        }

        events() {
            let $this = this;

            $(document).on('change', 'input.percentage-value', function (event) {
                $this.loadData(event);
            });
        }

        collectData(event) {
            let $this = this;

            $this.changesCounter++;

            $this.data.businessId = window.percentagesBusinessId;

            if (event && typeof event.target.id === 'string') {

                $this.lastCoordinatesElementId = event.target.id;

                $this.data.cells.push({
                    cellId: event.target.id,
                    phaseId: $('#' + event.target.id).data('phase-id'),
                    accountId: $('#' + event.target.id).data('account-id'),
                    cellValue: $('#' + event.target.id).val()
                });
            }

            if ($this.changesCounter) {
                $('#' + $this.changesCounterId).html('...changes ready for calculation <b>' + $this.changesCounter + '</b>').show();
            } else {
                $('#' + $this.changesCounterId).html('').hide();
            }

            if ($this.debug) {
                console.log('collectData', $this.data);
            }
        }
    }

    if ($('#percentagesTablePlace').length) {
        let PercentagesCalculatorClass = new PercentagesCalculator();
        PercentagesCalculatorClass.init();
    }
});
