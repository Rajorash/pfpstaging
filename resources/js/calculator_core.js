import {pfpFunctions} from "./pfp_functions.js";

// noinspection JSCheckFunctionSignatures
export class calculatorCore {
    constructor() {
        this.debug = false;
        this.changesCounter = 0;
        this.changesCounterId = 'processCounter';
        this.lastCoordinatesElementId = '';
        this.elementTablePlace = '';

        this.elementLoadingSpinner = $('#loadingSpinner');

        this.pfpFunctions = new pfpFunctions(); //external functions

        this.timeout = undefined; //just timeout object

        this.lastRowIndex = null; //last index of row in table
        this.lastColumnIndex = null; //last index of column in table

        this.delayProgressId = 'delay_progress';
        this.delayProgressInterval = undefined;
        this.delayProgressTimeIntervalMiliseconds = 20;

        this.autoSubmitDataAllowId = 'allow_auto_submit_data';
        this.autoSubmitDataAllow = false;
        this.autoSubmitDataAllowDefault = false;
        this.autoSubmitDataDelayId = 'delay_submit_data';
        this.autoSubmitDataDelayDefault = 2;
        this.autoSubmitDataDelay = this.autoSubmitDataDelayDefault;

        this.manualSubmitDataId = 'manualSubmitData';

        this.timeOutSeconds = 1000 * parseInt(this.autoSubmitDataDelay);  //default delay before send data to server

        this.heightModeDefaultSelector = '[name="block_different_height"]';
        this.heightModeDefault = 'window';
        this.heightMode = this.heightModeDefault;

        this.copyMoveClassName = 'pfp_copy_move_element';
        this.copyMoveAltKeyEnabled = false; //if ALt key is pressed

        this.windowCoordinates = {};

        this.hideTableDuringRecalculate = false;

        this.showRowsLevelID = 'show_rows_level';

        this.showRowsLevelTitles = {
            1: 'Accounts',
            2: 'Accounts with details',
            3: 'Sub Flows Total',
            4: 'All records'
        };
    }

    init() {
        let $this = this;

        $this.resetData();
        $this.events();

        $this.firstLoadData();
        $this.cursorForTableFill();
    }

    events() {
        let $this = this;

        $(document).on('change', '#' + $this.autoSubmitDataDelayId, function (event) {
            $this.autoSubmitDataDelay = $(this).val();
            $this.updateSubmitDataDelay();
            $this.timeOutSeconds = 1000 * parseFloat($this.autoSubmitDataDelay);
        });

        $(document).on('change', '#' + $this.autoSubmitDataAllowId, function (event) {
            $this.autoSubmitDataAllow = $(this).is(':checked');
            $this.updateAutoSubmitDataStatus();
        });

        $(document).on('change', $this.heightModeDefaultSelector, function (event) {
            $this.heightMode = $(this).val();
            $this.updateHeightMode();
        });

        $(document).on('click', '#' + $this.manualSubmitDataId, function () {
            $this.collectData();
            $this.ajaxLoadWorker();
            $this.hideTableDuringRender();

            return false;
        });

        $(document).on('change', '#' + $this.showRowsLevelID, function (event) {
            $this.changeDeepLevel();
        });

        //check and save state of Alt key
        $(window).on("keydown", function (event) {
            if (event.which === 18) {
                $this.copyMoveAltKeyEnabled = true;
                $('.' + $this.copyMoveClassName).removeClass('cursor-copy').addClass('cursor-move bg-yellow-300');
            }
        }).on("keyup", function (event) {
            $this.copyMoveAltKeyEnabled = false;
            $('.' + $this.copyMoveClassName).addClass('cursor-copy').removeClass('cursor-move bg-yellow-300');
        });

        $(document).keyup(function (event) {
            if (event.which === 13 && !$this.autoSubmitDataAllow) {
                $this.manualSubmitData(event);
            }
        });

        $(window).bind('unload', function () {
            if ($this.debug) {
                console.log('unload');
            }

            $this.hideTableDuringRecalculate = true;
            $this.hideTableDuringRender();
        });

        window.onbeforeunload = function (e) {
            if ($this.changesCounter) {
                if ($this.debug) {
                    console.log('onbeforeunload');
                }

                //highlight manual submit button
                $('#' + $this.changesCounterId)
                    .removeClass('opacity-50')
                    .parent()
                    .removeClass('text-dark_gray')
                    .addClass('bg-red-400 text-white');

                e.returnValue = '';
                e.preventDefault();
            }
        };
    }

    resetData() {
        let $this = this;

        if ($this.debug) {
            console.log('resetData');
        }

        $this.changesCounter = 0;
        $this.data = {};
        $this.data.cells = [];
    }

    showSpinner() {
        let $this = this;

        if ($this.debug) {
            console.log('showSpinner');
        }

        $('html, body').css({
            overflow: 'hidden',
            height: '100%'
        });

        $this.elementLoadingSpinner.show();
    }

    hideSpinner() {
        let $this = this;

        $('html, body').css({
            overflow: 'auto',
            height: 'auto'
        });

        if ($this.debug) {
            console.log('hideSpinner');
        }

        $this.elementLoadingSpinner.hide();
    }

    loadData(event) {
        let $this = this;

        if ($this.debug) {
            console.log('loadData');
        }

        $this.collectData(event);

        if ($this.debug) {
            console.log('Auto-submit data is: ' + $this.autoSubmitDataAllow);
        }

        if ($this.autoSubmitDataAllow) {
            //auto-submit data

            clearTimeout($this.timedOut);

            $this.timedOut = setTimeout(function () {
                $this.ajaxLoadWorker();
            }, $this.timeOutSeconds);
        }
    }

    manualSubmitData(event) {
        let $this = this;

        if ($this.debug) {
            console.log('manualSubmitData');
        }

        $this.ajaxLoadWorker();
    }

    progressBar() {
        let $this = this;

        if ($this.autoSubmitDataAllow) {
            if ($this.debug) {
                console.log('progressBar');
            }

            clearInterval($this.delayProgressInterval);

            $('#' + $this.delayProgressId).width($('#' + $this.delayProgressId).parent().width());

            $this.delayProgressInterval = setInterval(function () {
                let width = $('#' + $this.delayProgressId).width()
                    - $('#' + $this.delayProgressId).parent().width() / $this.timeOutSeconds * $this.delayProgressTimeIntervalMiliseconds;
                $('#' + $this.delayProgressId).width(width);
                if ($this.debug) {
                    console.log('progressBar after SetInterval');
                }
            }, $this.delayProgressTimeIntervalMiliseconds);
        }
    }

    hideProgressBar() {
        let $this = this;

        if ($this.debug) {
            console.log('hideProgressBar');
        }

        clearInterval($this.delayProgressInterval);
        $('#' + $this.delayProgressId).hide();
    }

    firstLoadData() {
        let $this = this;

        $this.collectData();
        $this.ajaxLoadWorker();
        $this.autoSubmitDataLoadData();
        $this.autoSubmitDataLoadState();
        $this.heightModeDataLoadData();
    }

    ajaxLoadWorker() {
        let $this = this;

        $.ajax({
            type: 'POST',
            url: $this.ajaxUrl,
            data: $this.data,
            async: true,
            beforeSend: function () {
                $this.hideTableDuringRender();
                $this.showSpinner();
            },
            success: function (data) {
                if ($this.debug) {
                    console.log('loadData', data);
                }
                $this.renderData(data);
                $this.readLastIndexes();
            },
            complete: function () {
                $this.hideSpinner();
                $this.resetData();
                $this.scrollToLatestPoint();
                //only for Allocations table
                // $this.forecastAutoFillValues();

                $this.afterLoadingDataHook();
            }
        });
    }

    hideTableDuringRender() {
        let $this = this;

        if ($this.hideTableDuringRecalculate) {
            $this.elementTablePlace.html('<div class="p-8 text-center opacity-50">...loading <span id="loading_timer_place"></span></div>');

            let secondsTimer = 0;
            let secondTimerPlace = $('#loading_timer_place');
            setInterval(function () {
                secondsTimer++;
                secondTimerPlace.html('...' + secondsTimer);
            }, 1000);
        }
    }

    readLastIndexes() {
        let $this = this;

        if ($this.debug) {
            console.log('readLastIndexes');
        }

        if ($('#php_lastData') && $('#php_lastData').length) {
            $this.lastRowIndex = parseInt($('#php_lastData').data('last_row_index'));
            $this.lastColumnIndex = parseInt($('#php_lastData').data('last_row_index'));
        }
    }

    renderData(data) {
        let $this = this;

        if ($this.debug) {
            console.log('renderData');
        }

        if (data.error.length === 0) {
            $this.elementTablePlace.html(data.html);

            if ($this.lastCoordinatesElementId) {
                $('#' + $this.lastCoordinatesElementId).focus();
                $('#' + $this.lastCoordinatesElementId).select();
            }

            $this.pfpFunctions.tableStickyHeader();
            $this.pfpFunctions.tableStickyFirstColumn();
        } else {
            $this.elementTablePlace.html('<p class="p-8 text-red-700 text-bold">' + data.error.join('<br/>') + '</p>');
        }
    }

    cursorForTableFill() {
        let $this = this;

        if ($this.debug) {
            console.log('cursorForTableFill');
        }

        $(document).on('keydown', '.cursor-fill-data', function (event) {
            const key = event.key; // "ArrowRight", "ArrowLeft", "ArrowUp", or "ArrowDown"

            let currentColumn = event.target.dataset.column || 0;
            let currentRow = event.target.dataset.row || 0;
            if ($this.debug) {
                console.log('currentColumn: ' + currentColumn + '; currentRow: ' + currentRow);
            }
            if (
                key === "ArrowLeft"
                || key === "ArrowRight"
                || key === "ArrowUp"
                || key === "ArrowDown"
            ) {
                $this.searchNextNotDisabledFiled(key, currentRow, currentColumn);
            }
        });
    }

    searchNextNotDisabledFiled(key, currentRow, currentColumn) {
        let $this = this;

        if ($this.debug) {
            console.log('searchNextNotDisabledFiled', key, currentRow, currentColumn, $this.lastRowIndex, $this.lastColumnIndex);
        }

        switch (key) {
            case "ArrowLeft":
                currentColumn--;
                break;
            case "ArrowRight":
                currentColumn++;
                break;
            case "ArrowUp":
                currentRow--
                break;
            case "ArrowDown":
                currentRow++;
                break;
        }

        let $newCell = $('[data-column="' + currentColumn + '"][data-row="' + currentRow + '"]');

        if ((!$newCell || $newCell.is(':disabled'))
            && currentRow > 0 && currentRow <= $this.lastRowIndex
            && currentColumn > 0 && currentColumn <= $this.lastColumnIndex
        ) {
            $this.searchNextNotDisabledFiled(key, currentRow, currentColumn);
        } else {
            $newCell.focus();
            $newCell.select();
        }
    }

    autoSubmitDataLoadData() {
        let $this = this;

        $('#' + $this.autoSubmitDataDelayId).val(($this.autoSubmitDataDelay > 0 ? $this.autoSubmitDataDelay : 2));
    }

    autoSubmitDataLoadState() {
        let $this = this;

        $('#' + $this.autoSubmitDataAllowId).prop('checked', $this.autoSubmitDataAllow);
    }

    heightModeDataLoadData() {
        let $this = this;

        if ($($this.heightModeDefaultSelector).length) {
            $($this.heightModeDefaultSelector + '[value="' + $this.heightMode + '"]').prop('checked', true);
        }
    }

    updateSubmitDataDelay() {
    }

    updateHeightMode() {
    }

    updateAutoSubmitDataStatus() {
        let $this = this;

        $this.hideProgressBar();
    }

    collectData() {
    }

    scrollToLatestPoint() {
        let $this = this;

        if ($this.debug) {
            console.log('scrollToLatestPoint');
        }

        if ($this.lastCoordinatesElementId && $('#' + $this.lastCoordinatesElementId).length) {
            let $elementOffset = $('#' + $this.lastCoordinatesElementId).offset();

            if ($elementOffset.hasOwnProperty('top') && $elementOffset.hasOwnProperty('left')) {
                if ($this.heightMode === 'full') {
                    if (
                        $this.windowCoordinates.hasOwnProperty('top')
                        && $this.windowCoordinates.hasOwnProperty('left')
                    ) {
                        $(window).scrollTop($this.windowCoordinates.top);
                        $(window).scrollLeft($this.windowCoordinates.left);
                    }
                }
            }
        }
    }

    afterLoadingDataHook() {
        let $this = this;

        this.changeDeepLevel();
    }

    dragAdnDropValues() {
        let $this = this;

        if ($this.debug) {
            console.log('dragAdnDropValues');
        }

        let coordinateX = 0, coordinateY = 0;

        document.addEventListener("dragover", function (event) {
            event.preventDefault();
            coordinateX = event.pageX;
            coordinateY = event.pageY;
        }, false);

        let copyMoveClassElement = document.getElementsByClassName($this.copyMoveClassName);
        for (let i = 0; i < copyMoveClassElement.length; i++) {
            copyMoveClassElement[i].addEventListener('dragend', function (event) {
                let sourceElement = event.target;
                let clientX = event.clientX, clientY = event.clientY;

                //only for mozilla https://bugzilla.mozilla.org/show_bug.cgi?id=505521#c80
                if (!clientX) {
                    clientX = coordinateX
                }
                if (!clientY) {
                    clientY = coordinateY
                }

                let targetElement = document.elementFromPoint(clientX, clientY);

                if (targetElement.classList.contains($this.copyMoveClassName)) {
                    let value = parseFloat(!!sourceElement.value ? sourceElement.value : 0);

                    if (!$this.copyMoveAltKeyEnabled) {
                        //sum of values
                        value += parseFloat(!!targetElement.value ? targetElement.value : 0);
                    }

                    targetElement.value = value;
                    sourceElement.value = 0;
                    targetElement.dispatchEvent(new Event('change', {bubbles: true}));
                    sourceElement.dispatchEvent(new Event('change', {bubbles: true}));
                }
            });
        }
    }

    renderButtonForManualSubmit() {
        return '<a href="#" id="manualSubmitData" class="bg-white hover:bg-gray-100 font-bold p-2 rounded text-red-700">Submit data</a>';
    }

    changeDeepLevel() {
        let $this = this;

        let $showRowsLevelSelectorSpan = $('label[for="' + $this.showRowsLevelID + '"] span');

        switch ($('#' + $this.showRowsLevelID).val()) {
            case '1':
                $('.level_1 .show_sub-elements').addClass('hidden');
                $('.level_1 .hide_sub-elements').removeClass('hidden');
                $('.level_2, .level_3, .sub_level').hide();
                $showRowsLevelSelectorSpan.text($this.showRowsLevelTitles[1]);
                break;
            case '2':
                $('.level_2').show();
                $('.level_2 .show_sub-elements').addClass('hidden');
                $('.level_2 .hide_sub-elements').removeClass('hidden');
                $('.level_3').hide();
                $('.sub_level').hide();
                $showRowsLevelSelectorSpan.text($this.showRowsLevelTitles[2]);
                break;
            case '3':
                $('.sub_level').show();
                $('.sub_level .show_sub-elements').addClass('hidden');
                $('.sub_level .hide_sub-elements').removeClass('hidden');
                $('.level_3').hide();
                $showRowsLevelSelectorSpan.text($this.showRowsLevelTitles[3]);
                break;
            case '4':
                $('.show_sub-elements').removeClass('hidden');
                $('.hide_sub-elements').addClass('hidden');
                $('.level_2, .level_3, .sub_level').show();
                $showRowsLevelSelectorSpan.text($this.showRowsLevelTitles[4]);
                break;
        }
    }
}
