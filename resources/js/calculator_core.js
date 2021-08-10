import {pfpFunctions} from "./pfp_functions.js";

export class calculatorCore {
    constructor() {
        this.debug = false;
        this.changesCounter = 0;
        this.changesCounterId = 'processCounter';
        this.lastCoordinatesElementId = '';

        this.elementLoadingSpinner = $('#loadingSpinner');

        this.pfpFunctions = new pfpFunctions(); //external functions

        this.timeOutSeconds = 2000; //default delay before send data to server
        this.timeout = undefined; //just timeout object

        this.lastRowIndex = null; //last index of row in table
        this.lastColumnIndex = null; //last index of column in table

        this.delayProgressId = 'delay_progress';
        this.delayProgressInterval = undefined;
        this.delayProgressTimeIntervalMiliseconds = 20;

        this.autoSubmitDataDelayId = 'delay_submit_data';
        this.autoSubmitDataDelayDefault = 2;
        this.autoSubmitDataDelay = this.autoSubmitDataDelayDefault;

        this.heightModeDefaultSelector = '[name="block_different_height"]';
        this.heightModeDefault = 'full';
        this.heightMode = this.heightModeDefault;
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
            $this.timeOutSeconds = 1000 * parseInt($this.autoSubmitDataDelay);
        });

        $(document).on('change', $this.heightModeDefaultSelector, function (event) {
            $this.heightMode = $(this).val();
            $this.updateHeightMode();
        });
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

        $this.elementLoadingSpinner.hide();
    }

    loadData(event) {
        let $this = this;

        $this.collectData(event);

        clearTimeout($this.timedOut);

        $this.timedOut = setTimeout(function () {
            $this.ajaxLoadWorker();
        }, $this.timeOutSeconds);
    }

    progressBar() {
        let $this = this;

        clearInterval($this.delayProgressInterval);

        $('#' + $this.delayProgressId).width($('#' + $this.delayProgressId).parent().width());

        $this.delayProgressInterval = setInterval(function () {
            let width = $('#' + $this.delayProgressId).width()
                - $('#' + $this.delayProgressId).parent().width() / $this.timeOutSeconds * $this.delayProgressTimeIntervalMiliseconds;
            $('#' + $this.delayProgressId).width(width);
        }, $this.delayProgressTimeIntervalMiliseconds);
    }

    firstLoadData() {
        let $this = this;

        $this.collectData();
        $this.ajaxLoadWorker();
        $this.autoSubmitDataLoadData();
        $this.heightModeDataLoadData();
    }

    ajaxLoadWorker() {
        let $this = this;

        $.ajax({
            type: 'POST',
            url: $this.ajaxUrl,
            data: $this.data,
            beforeSend: function () {
                $this.showSpinner()
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
            }
        });
    }

    readLastIndexes() {
        let $this = this;

        if ($('#php_lastData') && $('#php_lastData').length) {
            $this.lastRowIndex = parseInt($('#php_lastData').data('last_row_index'));
            $this.lastColumnIndex = parseInt($('#php_lastData').data('last_row_index'));
        }
    }

    renderData(data) {
        let $this = this;

        if (data.error.length === 0) {
            $this.elementTablePlace.html(data.html);

            if ($this.lastCoordinatesElementId) {
                $('#' + $this.lastCoordinatesElementId).focus();
            }

            $this.pfpFunctions.tableStickyHeader();
            $this.pfpFunctions.tableStickyFirstColumn();
        } else {
            $this.elementTablePlace.html('<p class="p-8 text-red-700 text-bold">' + data.error.join('<br/>') + '</p>');
        }
    }

    cursorForTableFill() {
        let $this = this;

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
            console.log(key, currentRow, currentColumn, $this.lastRowIndex, $this.lastColumnIndex);
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

}
