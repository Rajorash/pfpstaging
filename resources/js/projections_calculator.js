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

            this.tableId = 'projectionsTablePlace';
            this.prevPageId = 'prev_page';
            this.nextPageId = 'next_page';
        }

        events() {
            let $this = this;
            super.events();

            $(document).on('change', '#currentProjectionsRange, #endDate', function (event) {
                $this.pageDate = null; //reset current page
                $this.way = null; //reset current page
                $this.loadData(event);
            });

            $(document).on('click', '#' + $this.recalculateButtonId, function (event) {
                $this.recalculateAllDataState = true;
                $this.loadData(event);
                return false;
            });

            $(document).on('click', '#' + $this.nextPageId, function () {
                if ($('#' + $this.tableId).find('thead').data('right-date')) {
                    $this.pageDate = $('#' + $this.tableId).find('thead').data('right-date');
                    $this.way = 'future';

                    $this.collectData();
                    $this.ajaxLoadWorker();
                    $this.hideTableDuringRender();
                }

                return false;
            });
            $(document).on('click', '#' + $this.prevPageId, function () {
                if ($('#' + $this.tableId).find('thead').data('left-date')) {
                    $this.pageDate = $('#' + $this.tableId).find('thead').data('left-date');
                    $this.way = 'past';

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
            $this.data.pageDate = $this.pageDate;
            $this.data.way = $this.way;

            if ($this.debug) {
                console.log('collectData', $this.data);
            }
        }

        prevNextButtons() {
            let $this = this;

            let $attributePlace = $('#' + $this.tableId).find('thead');

            if ($attributePlace.data('left-date')) {
                $('#' + $this.prevPageId).find('.place').text($attributePlace.data('left-date-title'));
                $('#' + $this.prevPageId).parent().show();
            } else {
                $('#' + $this.prevPageId).find('.place').text('');
                $('#' + $this.prevPageId).parent().hide();
            }

            if ($attributePlace.data('right-date')) {
                $('#' + $this.nextPageId).find('.place').text($attributePlace.data('right-date-title'));
                $('#' + $this.nextPageId).parent().show();
            } else {
                $('#' + $this.nextPageId).find('.place').text('');
                $('#' + $this.nextPageId).parent().hide();
            }
        }
    }

    if ($('#projectionsTablePlace').length) {
        let ProjectionsCalculatorClass = new ProjectionsCalculator();
        ProjectionsCalculatorClass.init();
    }
});
