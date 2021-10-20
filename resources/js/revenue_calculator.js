import {calculatorCore} from "./calculator_core";

$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class RevenueCalculator extends calculatorCore {
        constructor() {
            super();

            this.ajaxUrl = window.revenueControllerUpdate;
            this.elementTablePlace = $('#revenueTablePlace');
            this.autoSubmitDataAllow = true;
            this.timeOutSeconds = 2000;
        }

        events() {
            let $this = this;
            super.events();

            $(document).on('change', '#revenueStartDate, #revenueCurrentRangeValue, #revenueTablePlace input', function (event) {
                $this.loadData(event);
                $this.progressBar();
            });

        }

        collectData(event) {
            let $this = this;

            $this.changesCounter++;

            $this.data.businessId = $('#revenueBusinessId').val();
            $this.data.startDate = $('#revenueStartDate').val();
            $this.data.rangeValue = $('#revenueCurrentRangeValue').val();

            // if (event && typeof event.target.id === 'string') {
            //
            //     $this.lastCoordinatesElementId = event.target.id;
            //     $this.windowCoordinates = {
            //         top: $(window).scrollTop(),
            //         left: $(window).scrollLeft()
            //     }
            //
            //     if (event.target.id !== 'currentRangeValue'
            //         && event.target.id !== 'startDate') {
            //         $this.data.cells.push({
            //             cellId: event.target.id,
            //             cellValue: $('#' + event.target.id).val()
            //         });
            //     }
            // }
            //
            // if ($this.changesCounter) {
            //     $('#' + $this.changesCounterId).html('...changes ready for calculation: <b>' + $this.changesCounter + '</b>'
            //         + '<br/>' + $this.renderButtonForManualSubmit()).show();
            // } else {
            //     $('#' + $this.changesCounterId).html('').hide();
            // }

            // if ($this.debug) {
                console.log('collectData', $this.data);
            // }
        }

        getTargetSelectorForForecast(row, col) {
            return '[data-row="' + row + '"][data-column="' + col + '"]';
        }

    }

    if ($('#revenueTablePlace').length) {
        let RevenueCalculatorClass = new RevenueCalculator();
        RevenueCalculatorClass.init();
    }
});
