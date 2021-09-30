import {calculatorCore} from "./calculator_core";

$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class ProjectionsCalculator extends calculatorCore {
        constructor() {
            super();

            this.ajaxUrl = window.projectionsControllerUpdate;
            this.elementTablePlace = $('#projectionsTablePlace');

            this.recalculateButtonId = 'recalculate_pf';
            this.recalculateAllDataState = false;

            this.hideTableDuringRecalculate = true;
            this.timeOutSeconds = 0;

            this.autoSubmitDataAllow = true;
        }

        events() {
            let $this = this;
            super.events();

            $(document).on('change', '#currentProjectionsRange, #endDate', function (event) {
                $this.loadData(event);
            });

            $(document).on('click', '#' + $this.recalculateButtonId, function (event) {
                $this.recalculateAllDataState = true;
                $this.loadData(event);
                return false;
            });
        }

        resetData() {
            let $this = this;

            super.resetData();
            $this.recalculateAllDataState = false;
        }


        renderData(data) {
            let $this = this;

            super.renderData(data);

            if (data.error.length === 0) {

                $('#endDate').val(data.end_date);

            }
            console.log(data);
        }

        collectData(event) {
            let $this = this;

            $this.changesCounter++;

            $this.windowCoordinates = {
                top: $(window).scrollTop(),
                left: $(window).scrollLeft()
            };

            $this.data.businessId = $('#businessId').val();
            $this.data.rangeValue = $('#currentProjectionsRange').val();
            $this.data.endDate = $('#endDate').val();
            $this.data.recalculateAll = $this.recalculateAllDataState ? 1 : 0;

            if ($this.debug) {
                console.log('collectData', $this.data);
            }
        }
    }

    if ($('#projectionsTablePlace').length) {
        let ProjectionsCalculatorClass = new ProjectionsCalculator();
        ProjectionsCalculatorClass.init();
    }
});
