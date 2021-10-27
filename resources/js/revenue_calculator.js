import {calculatorCore} from "./calculator_core";

$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class RevenueCalculator extends calculatorCore {
        revenueControllerUpdate;
        revenueControllerSave;

        constructor() {
            super();

            this.ajaxUrl = window.revenueControllerUpdate;
            this.ajaxUrlSave = window.revenueControllerSave;
            this.elementTablePlace = $('#revenueTablePlace');
            this.autoSubmitDataAllow = true;
            this.timeOutSeconds = 2000;
        }

        events() {
            let $this = this;
            super.events();

            $(document).on('change', '#revenueStartDate, #revenueCurrentRangeValue', function (event) {
                $this.autoSubmitDataAllow = true;
                $this.loadData(event);
                $this.progressBar();
            });

            $(document).on('change', 'input.flow_cell', function (event) {
                $this.autoSubmitDataAllow = false;
                $this.recalculateRevenueTable();
                $this.saveData(event);
            });

            Livewire.on('reloadRevenueTable', function () {
                $this.firstLoadData();
            })
        }

        collectData(event) {
            let $this = this;

            $this.changesCounter++;

            $this.data.businessId = $('#revenueBusinessId').val();
            $this.data.startDate = $('#revenueStartDate').val();
            $this.data.rangeValue = $('#revenueCurrentRangeValue').val();

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

            if ($this.debug) {
                console.log('collectData', $this.data);
            }
        }

        getTargetSelectorForForecast(row, col) {
            return '[data-row="' + row + '"][data-column="' + col + '"]';
        }

        recalculateRevenueTable() {
            let $this = this;

            //let array for other types
            $.each(['flow'], function (i, $class) {
                // console.log("Class " + $class);
                if ($('.' + $class + '_total').length) {
                    $('.' + $class + '_total').each(function () {
                        let $result = 0;

                        $('.' + $class + '_cell[data-column="' + $(this).data('column') + '"]').each(function () {
                            // preven NaN error on deleting value
                            let value = $(this).val().length == 0 ? 0 : $(this).val();

                            $result += ($(this).data('negative') ? -1 : 1)
                                * parseFloat(($(this).data('certainty') ?? 100)) / 100
                                * parseFloat(value);
                        });

                        $(this).val($result.toFixed(0));
                    });
                }

            });

            $('.revenue_total').each(function () {
                let $revenue = 0;
                let $column = $(this).data('column');

                $.each(['flow'], function (i, $class) {
                    let $selector = '.' + $class + '_total[data-column="' + $column + '"]';
                    if ($($selector).length) {
                        $revenue += parseFloat($($selector).val() ?? 0);
                    } else {
                        $revenue += 0;
                    }
                });

                $(this).val($revenue.toFixed(0));
            });

        }

        renderData(data) {
            super.renderData(data);

            this.recalculateRevenueTable();
        }

        //rewrite basically usage
        manualSubmitData(event) {
            let $this = this;

            if ($this.debug) {
                console.log('manualSubmitData');
            }

            $this.saveData(event);
        }

        saveData(event) {
            let $this = this;

            $this.collectData(event);

            $.ajax({
                type: 'POST',
                url: $this.ajaxUrlSave,
                data: $this.data,
                async: true,
                beforeSend: function () {
                },
                success: function (data) {
                    if ($this.debug) {
                        console.log('loadDataAfterSave', data);
                    }
                },
                complete: function () {
                    $this.resetData();
                }
            });


            $this.resetData();
        }
    }

    if ($('#revenueTablePlace').length) {
        let RevenueCalculatorClass = new RevenueCalculator();
        RevenueCalculatorClass.init();
    }
});
