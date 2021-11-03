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

            this.heightMode = $.cookie('allocation_heightMode') !== undefined
                ? $.cookie('allocation_heightMode')
                : this.heightModeDefault;
        }

        events() {
            let $this = this;
            super.events();

            $(document).on('change', '#startDate, #currentRangeValue', function (event) {
                $this.autoSubmitDataAllow = true;
                $this.timeOutSeconds = 0;
                $this.loadData(event);
                $this.progressBar();
            });

            Livewire.on('reloadRevenueTable', function () {
                $this.firstLoadData();
            })

            $(document).on('change', '#allocationsNewTablePlace input', function (event) {
                $this.updateValue(event);
            });

            $(document).on('change', '#show_rows_level', function (event) {
                $this.changeDeepLevel();
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
                    $('.allocation-highlight').removeClass('allocation-highlight');
                },
                success: function (data) {
                    if ($this.debug) {
                        console.log('loadData', data);
                    }
                    $this.renderUpdatedData(data);
                },
                complete: function () {
                    $this.updateData = {};
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
                    if ($elements.val() !== value) {
                        $elements.val(value)
                            .removeClass('allocation-negative-value')
                            .addClass((value < 0 ? 'allocation-negative-value' : ''))
                            .addClass('allocation-highlight');
                    }
                });
                $this.changeDeepLevel();
            } else {
                $this.elementTablePlace.html('<p class="p-8 text-red-700 text-bold">' + data.error.join('<br/>') + '</p>');
            }
        }

        manualSubmitData(event) {
            return false;
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

        switchHeightMode() {
            let $this = this;

            if ($this.heightMode === 'full') {
                $('.block_different_height').height('auto');
            } else {
                let height = $(window).height() - 50;
                let blockDifferentHeight = $('.block_different_height');

                if (blockDifferentHeight.offset()) {
                    height -= blockDifferentHeight.offset().top;
                }

                blockDifferentHeight.height(height);
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

        changeDeepLevel() {
            switch ($('#show_rows_level').val()) {
                case '1':
                    $('.level_2, .level_3').hide();
                    $('label[for="show_rows_level"] span').text('Accounts');
                    break;
                case '2':
                    $('.level_2').show();
                    $('.level_3').hide();
                    $('label[for="show_rows_level"] span').text('Accounts with details');
                    break;
                case '3':
                    $('.level_2, .level_3').show();
                    $('label[for="show_rows_level"] span').text('All records');
                    break;
            }
        }

        firstLoadData() {
            super.firstLoadData();

            this.changeDeepLevel();
        }
    }

    if ($('#allocationsNewTablePlace').length) {
        let AllocationCalculatorNewClass = new AllocationCalculatorNew();
        AllocationCalculatorNewClass.init();
    }
});
