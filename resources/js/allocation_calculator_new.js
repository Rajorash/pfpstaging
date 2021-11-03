import {calculatorCore} from "./calculator_core";

$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class AllocationCalculatorNew extends calculatorCore {
        allocationsNewControllerUpdate;

        constructor() {
            super();

            this.ajaxUrl = window.allocationsNewControllerUpdate;
            this.elementTablePlace = $('#allocationsNewTablePlace');

            this.updateData = {};

            // this.autoSubmitDataDelay = $.cookie('allocation_autoSubmitDataDelay') !== undefined
            //     ? parseInt($.cookie('allocation_autoSubmitDataDelay'))
            //     : this.autoSubmitDataDelayDefault;
            //
            // this.autoSubmitDataAllow = $.cookie('allocation_autoSubmitDataAllow') !== undefined
            //     ? $.cookie('allocation_autoSubmitDataAllow')
            //     : this.autoSubmitDataAllowDefault;
            //
            // this.timeOutSeconds = 1000 * this.autoSubmitDataDelay;
            //
            // this.heightMode = $.cookie('allocation_heightMode') !== undefined
            //     ? $.cookie('allocation_heightMode')
            //     : this.heightModeDefault;
            //
            // this.allowForecastDoubleClickId = 'allow_forecast_double_click';
        }

        events() {
            let $this = this;
            super.events();

            $(document).on('change', '#startDate, #currentRangeValue', function (event) {
                //TODO: save old values
                $this.autoSubmitDataAllow = true;
                $this.timeOutSeconds = 0;
                $this.loadData(event);
                $this.progressBar();
            });
            //
            // $(document).on('dblclick', '.pfp_forecast_value', function () {
            //     if ($('#' + $this.allowForecastDoubleClickId).is(':checked') && !$(this).hasClass('pfp_forecast_already_added')) {
            //         let targetSelector = $this.getTargetSelectorForForecast($(this).data('for_row'), $(this).data('for_column'));
            //         $(targetSelector).val(parseFloat($(targetSelector).val()) + parseFloat($(this).val()))
            //             .select(false)
            //             .change();
            //         $(this).addClass('pfp_forecast_already_added');
            //     }
            // });

            Livewire.on('reloadRevenueTable', function () {
                $this.firstLoadData();
            })

            $(document).on('change', '#allocationsNewTablePlace input', function (event) {
                $this.updateValue(event);
            });
        }

        updateValue(event) {
            let $this = this;

            $this.updateData.businessId = $('#businessId').val();
            $this.updateData.startDate = $('#startDate').val();
            $this.updateData.rangeValue = $('#currentRangeValue').val();
            $this.updateData.cellId = event.target.id;
            $this.updateData.cellValue = parseFloat($('#' + event.target.id).val());
            $this.updateData.returnType = 'json';

            if ($this.debug) {
                console.log($this.updateData);
            }

            $.ajax({
                type: 'POST',
                url: $this.ajaxUrl,
                data: $this.updateData,
                async: true,
                beforeSend: function () {
                    // $this.hideTableDuringRender();
                    // $this.showSpinner();
                    $('.allocation-highlight').removeClass('allocation-highlight');
                },
                success: function (data) {
                    if ($this.debug) {
                        console.log('loadData', data);
                    }
                    $this.renderUpdatedData(data);
                    // $this.renderData(data);
                    // $this.readLastIndexes();
                },
                complete: function () {
                    $this.updateData = {};

                    // $this.hideSpinner();
                    // $this.resetData();
                    // $this.scrollToLatestPoint();
                    //only for Allocations table
                    // $this.forecastAutoFillValues();
                }
            });
        }

        renderUpdatedData(data) {
            let $this = this;

            if ($this.debug) {
                console.log('renderData');
            }

            if (data.error.length === 0) {
                $.each(data['data'], function (cellId, value) {
                    let $elements = $('#' + cellId);
                    console.log($elements.val(), value, $elements.val() !== value);
                    if ($elements.val() !== value) {
                        $elements.val(value)
                            .removeClass('allocation-negative-value')
                            .addClass((value < 0 ? 'allocation-negative-value' : ''))
                            .addClass('allocation-highlight');
                    }
                });
            } else {
                $this.elementTablePlace.html('<p class="p-8 text-red-700 text-bold">' + data.error.join('<br/>') + '</p>');
            }
        }

        manualSubmitData(event) {

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

                // if (event.target.id !== 'currentRangeValue'
                //     && event.target.id !== 'startDate') {
                //     $this.data.cells.push({
                //         cellId: event.target.id,
                //         cellValue: $('#' + event.target.id).val()
                //     });
                // }
            }

            // if ($this.changesCounter) {
            //     $('#' + $this.changesCounterId).html('...changes ready for calculation: <b>' + $this.changesCounter + '</b>'
            //         + '<br/>' + $this.renderButtonForManualSubmit()).show();
            // } else {
            //     $('#' + $this.changesCounterId).html('').hide();
            // }

            if ($this.debug) {
                console.log('collectData', $this.data);
            }
        }

        // updateSubmitDataDelay() {
        //     let $this = this;
        //
        //     $.cookie('allocation_autoSubmitDataDelay', $this.autoSubmitDataDelay, {expires: 14});
        // }
        //
        // updateAutoSubmitDataStatus() {
        //     super.updateAutoSubmitDataStatus();
        //     let $this = this;
        //
        //     $.cookie('allocation_autoSubmitDataAllow', $this.autoSubmitDataAllow, {expires: 14});
        // }
        //
        // heightModeDataLoadData() {
        //     super.heightModeDataLoadData();
        //     let $this = this;
        //
        //     $this.switchHeightMode();
        // }
        //
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
        //
        // updateHeightMode() {
        //     let $this = this;
        //
        //     $this.switchHeightMode();
        //
        //     $.cookie('allocation_heightMode', $this.heightMode, {expires: 14});
        // }
        //
        // getTargetSelectorForForecast(row, col) {
        //     return '[data-row="' + row + '"][data-column="' + col + '"]';
        // }
        //
        // forecastAutoFillValues() {
        //     let $this = this;
        //
        //     $('.pfp_forecast_value').each(function (i, element) {
        //         let targetSelector = $this.getTargetSelectorForForecast($(element).data('for_row'), $(element).data('for_column'));
        //         if (parseFloat($(targetSelector).val()) === 0 || $(targetSelector).hasClass('pfp_allow_to_forecast_autofill')) {
        //             $(targetSelector).val(parseFloat($(targetSelector).val()) + parseFloat($(element).val())).select(false)
        //                 .addClass('pfp_allow_to_forecast_autofill')
        //                 .change();
        //             $(element).addClass('pfp_forecast_already_added');
        //         }
        //     });
        //
        //     $('.pfp_allow_to_forecast_autofill').removeClass('pfp_allow_to_forecast_autofill');
        // }
    }

    if ($('#allocationsNewTablePlace').length) {
        let AllocationCalculatorNewClass = new AllocationCalculatorNew();
        AllocationCalculatorNewClass.init();
    }
});
