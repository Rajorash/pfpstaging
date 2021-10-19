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

            this.heightMode = $.cookie('revenue_heightMode') !== undefined
                ? $.cookie('revenue_heightMode')
                : this.heightModeDefault;

        }

        events() {
            let $this = this;
            super.events();

            $(document).on('change', '#startDate, #currentRangeValue, #revenueTablePlace input', function (event) {
                $this.loadData(event);
                $this.progressBar();
            });

        }

        collectData(event) {
            let $this = this;

            $this.changesCounter++;

            $this.data.businessId = $('#businessId').val();
            $this.data.startDate = $('#startDate').val();
            $this.data.rangeValue = $('#currentRangeValue').val();

            if (event && typeof event.target.id === 'string') {

                $this.lastCoordinatesElementId = event.target.id;
                $this.windowCoordinates = {
                    top: $(window).scrollTop(),
                    left: $(window).scrollLeft()
                }

                if (event.target.id !== 'currentRangeValue'
                    && event.target.id !== 'startDate') {
                    $this.data.cells.push({
                        cellId: event.target.id,
                        cellValue: $('#' + event.target.id).val()
                    });
                }
            }

            if ($this.changesCounter) {
                $('#' + $this.changesCounterId).html('...changes ready for calculation: <b>' + $this.changesCounter + '</b>'
                    + '<br/>' + $this.renderButtonForManualSubmit()).show();
            } else {
                $('#' + $this.changesCounterId).html('').hide();
            }

            if ($this.debug) {
                console.log('collectData', $this.data);
            }
        }

        heightModeDataLoadData() {
            super.heightModeDataLoadData();
            let $this = this;

            $this.switchHeightMode();
        }

        // switchHeightMode() {
        //     let $this = this;
        //
        //     if ($this.heightMode === 'full') {
        //         $('.block_different_height').height('auto');
        //     } else {
        //         let height = $(window).height() - 50;
        //         let blockDifferentHeight = $('.block_different_height');
        //
        //         if (blockDifferentHeight.offset()) {
        //             height -= blockDifferentHeight.offset().top;
        //         }
        //
        //         blockDifferentHeight.height(height);
        //     }
        //
        //     setTimeout(function () {
        //         $(".global_nice_scroll").getNiceScroll().resize();
        //     }, 500);
        // }

        updateHeightMode() {
            let $this = this;

            $this.switchHeightMode();

            $.cookie('revenue_heightMode', $this.heightMode, {expires: 14});
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
