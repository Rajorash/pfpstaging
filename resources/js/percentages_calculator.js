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

            this.autoSubmitDataDelay = $.cookie('percentage_autoSubmitDataDelay') !== undefined
                ? parseInt($.cookie('percentage_autoSubmitDataDelay'))
                : this.autoSubmitDataDelayDefault;

            this.timeOutSeconds = 1000 * parseInt(this.autoSubmitDataDelay);
        }

        events() {
            let $this = this;
            super.events();

            $(document).on('change', 'input.percentage-value', function (event) {
                $this.loadData(event);
                $this.progressBar();
            });
        }

        collectData(event) {
            let $this = this;

            $this.changesCounter++;

            $this.data.businessId = window.percentagesBusinessId;

            if (event && typeof event.target.id === 'string') {

                $this.lastCoordinatesElementId = event.target.id;
                $this.windowCoordinates = {
                    top: $(window).scrollTop(),
                    left: $(window).scrollLeft()
                };

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

        updateSubmitDataSwitcher() {
            let $this = this;

            $.cookie('percentage_autoSubmitDataSwitcher', $this.autoSubmitDataSwitcher, {expires: 14});
        }

        updateSubmitDataDelay() {
            let $this = this;

            $.cookie('percentage_autoSubmitDataDelay', $this.autoSubmitDataDelay, {expires: 14});
        }
    }

    if ($('#percentagesTablePlace').length) {
        let PercentagesCalculatorClass = new PercentagesCalculator();
        PercentagesCalculatorClass.init();
    }
});
