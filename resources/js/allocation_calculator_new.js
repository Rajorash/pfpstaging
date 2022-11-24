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

            this.openModalPreviousState = {};
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

            Livewire.on('openModal', function (type, params) {
                $this.openModalPreviousState = params;
            })

            $(document).on('change', '#allocationsNewTablePlace input', function (event) {
                $this.updateValue(event);
            });

            $(document).on('click', '.show_hide_sub-elements', function () {
                let $elementThis = $(this);

                let $currentStateIsOpen = $elementThis.hasClass('opened');
                let $accountId = $elementThis.data('account_id');

                let $classNameToProcess = $elementThis.closest('tr').hasClass('level_1')
                    ? '.level_2' + '[data-account_id="' + $accountId + '"],'
                    : '';

                let $classNameSubToProcess = $elementThis.closest('tr').hasClass('sub_level') ? '.level_3' + '[data-account_id="' + $accountId + '"]' : '';

                $classNameToProcess += '.level_3' + '[data-account_id="' + $accountId + '"],';
                $classNameToProcess += '.sub_level' + '[data-account_id="' + $accountId + '"]';

                if ($currentStateIsOpen) {
                    if($classNameSubToProcess){
                       $($classNameSubToProcess).hide();
                    }else{
                       $($classNameToProcess).hide();
                    }
                    $elementThis.find('.hide_sub-elements').removeClass('hidden');
                    $elementThis.find('.show_sub-elements').addClass('hidden');
                    $elementThis.removeClass('opened');
                } else {
                    $($classNameToProcess).show();
                    $elementThis.find('.hide_sub-elements').addClass('hidden');
                    $elementThis.find('.show_sub-elements').removeClass('hidden');
                    $elementThis.addClass('opened');
                }
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
                        $elements.val(value).addClass('allocation-highlight');

                        if ($elements.hasClass('can_apply_negative_class')) {
                            $elements.removeClass('allocation-negative-value')
                                .addClass((value < 0 ? 'allocation-negative-value' : ''));
                        }
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

        afterLoadingDataHook() {
            super.afterLoadingDataHook();

            let $this = this;

            $this.dragAdnDropValues();
            $this.scrollToLatestOpenModal();
        }

        scrollToLatestOpenModal() {
            let $this = this;

            if ($this.debug) {
                console.log('scrollToLatestOpenModal');
            }

            if ($this.openModalPreviousState && $this.openModalPreviousState.hasOwnProperty('accountId')) {
                let $accountOffset = $('tr[data-account_id="' + $this.openModalPreviousState.accountId + '"]').offset();

                if ($accountOffset.hasOwnProperty('top')) {
                    if ($this.heightMode === 'full') {
                        $(window).scrollTop($accountOffset.top - 50);
                    }
                }
            }
        }
    }

    if ($('#allocationsNewTablePlace').length) {
        let AllocationCalculatorNewClass = new AllocationCalculatorNew();
        AllocationCalculatorNewClass.init();
    }
});
