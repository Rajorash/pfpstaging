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

            this.tableId = 'projection_table';
            this.prevPageId = 'prev_page';
            this.nextPageId = 'next_page';

            this.currentPage = 1;
        }

        events() {
            let $this = this;
            super.events();

            $(document).on('change', '#currentProjectionsRange, #endDate', function (event) {
                $this.currentPage = 1; //reset current page
                $this.loadData(event);
            });

            $(document).on('click', '#' + $this.recalculateButtonId, function (event) {
                $this.recalculateAllDataState = true;
                $this.loadData(event);
                return false;
            });

            $(document).on('click', '#' + $this.nextPageId, function () {
                if ($('#' + $this.tableId).data('next-page')) {
                    $this.currentPage = parseInt($('#' + $this.tableId).data('next-page'));

                    $this.collectData();
                    $this.ajaxLoadWorker();
                    $this.hideTableDuringRender();
                }

                return false;
            });
            $(document).on('click', '#' + $this.prevPageId, function () {
                if ($('#' + $this.tableId).data('prev-page')) {
                    $this.currentPage = parseInt($('#' + $this.tableId).data('prev-page'));

                    $this.collectData();
                    $this.ajaxLoadWorker();
                    $this.hideTableDuringRender();
                }

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

                $this.prevNextButtons();
            }

            if ($this.debug) {
                console.log('renderData', data);
            }
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
            $this.data.page = $this.currentPage;

            if ($this.debug) {
                console.log('collectData', $this.data);
            }
        }

        prevNextButtons() {
            let $this = this;

            let $table = $('#' + $this.tableId);

            if ($table.data('prev-page')) {
                $('#' + $this.prevPageId).attr('title', $table.data('prev-page-title')).parent().show();
            } else {
                $('#' + $this.prevPageId).attr('title', '').parent().hide();
            }

            if ($table.data('next-page')) {
                $('#' + $this.nextPageId).attr('title', $table.data('next-page-title')).parent().show();
            } else {
                $('#' + $this.nextPageId).attr('title', '').parent().hide();
            }
        }
    }

    if ($('#projectionsTablePlace').length) {
        let ProjectionsCalculatorClass = new ProjectionsCalculator();
        ProjectionsCalculatorClass.init();
    }
});
