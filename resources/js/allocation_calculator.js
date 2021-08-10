import {calculatorCore} from "./calculator_core";

$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class AllocationCalculator extends calculatorCore {
        constructor() {
            super();

            this.ajaxUrl = window.allocationsControllerUpdate;
            this.elementTablePlace = $('#allocationTablePlace');

            this.autoSubmitDataDelay = $.cookie('allocation_autoSubmitDataDelay') !== undefined
                ? parseInt($.cookie('allocation_autoSubmitDataDelay'))
                : this.autoSubmitDataDelayDefault;

            this.heightMode = $.cookie('allocation_heightMode') !== undefined
                ? $.cookie('allocation_heightMode')
                : this.heightModeDefault;
        }

        events() {
            let $this = this;
            super.events();

            $(document).on('change', '#startDate, #currentRangeValue, #allocationTablePlace input', function (event) {
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

                if (event.target.id !== 'currentRangeValue'
                    && event.target.id !== 'startDate') {
                    $this.data.cells.push({
                        cellId: event.target.id,
                        cellValue: $('#' + event.target.id).val()
                    });
                }
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

        updateSubmitDataDelay() {
            let $this = this;

            $.cookie('allocation_autoSubmitDataDelay', $this.autoSubmitDataDelay, {expires: 14});
        }

        heightModeDataLoadData() {
            super.heightModeDataLoadData();
            let $this = this;

            $this.switchHeightMode();
        }

        switchHeightMode() {
            let $this = this;

            if ($this.heightMode === 'full') {
                $('.block_different_height').height('auto');
            } else {
                let height = $(window).height() - 20;

                if ($('.block_different_height').offset()) {
                    height -= $('.block_different_height').offset().top;
                }

                $('.block_different_height').height(height);
            }

            setTimeout(function () {
                $(".global_nice_scroll").getNiceScroll().resize();
            }, 500);
        }

        updateHeightMode() {
            let $this = this;

            $this.switchHeightMode();

            $.cookie('allocation_heightMode', $this.heightMode, {expires: 14});
        }
    }

    if ($('#allocationTablePlace').length) {
        let AllocationCalculatorClass = new AllocationCalculator();
        AllocationCalculatorClass.init();
    }
});
