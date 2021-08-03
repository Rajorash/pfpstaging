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
    }

    init() {
        let $this = this;

        $this.resetData();
        $this.events();

        $this.firstLoadData();
        $this.cursorForTableFill();
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
        console.log('found');
        $this.collectData(event);

        clearTimeout($this.timedOut);
        $this.timedOut = setTimeout(function () {
            $this.ajaxLoadWorker();
        }, $this.timeOutSeconds);
    }

    firstLoadData() {
        let $this = this;

        $this.collectData();
        $this.ajaxLoadWorker();
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
            },
            complete: function () {
                $this.hideSpinner();
                $this.resetData();
            }
        });
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
        $(document).on('keydown', '.cursor-fill-data', function (event) {
            const key = event.key; // "ArrowRight", "ArrowLeft", "ArrowUp", or "ArrowDown"

            let currentColumn = event.target.dataset.column || 0;
            let currentRow = event.target.dataset.row || 0;

            if (
                key === "ArrowLeft"
                || key === "ArrowRight"
                || key === "ArrowUp"
                || key === "ArrowDown"
            ) {
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
                if ($newCell.length) {
                    $newCell.focus();
                    $newCell.select();
                }
            }
        });
    }
}
