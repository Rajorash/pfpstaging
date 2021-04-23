import {pfpFunctions} from "./pfp_functions.js";

$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class PercentagesCalculator {
        constructor(pfpFunctions) {
            this.debug = false;

            this.ajaxUrl = window.percentagesControllerUpdate;
            this.elementPercentagesTablePlace = $('#percentagesTablePlace');
            this.elementLoadingSpinner = $('#loadingSpinner');

            this.changesCounter = 0;
            this.changesCounterId = 'processCounter';
            this.lastCoordinatesElementId = '';

            this.pfpFunctions = pfpFunctions;

            this.timeout;
        }

        init() {
            let $this = this;

            $this.resetData();
            $this.events();

            $this.firstLoadData();
        }

        events() {
            let $this = this;

            $(document).on('change', 'input.percentage-value', function (event) {
                $this.loadData(event);
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

        collectData(event) {
            let $this = this;

            $this.changesCounter++;

            $this.data.businessId = window.percentagesBusinessId;

            if (event && typeof event.target.id === 'string') {

                $this.lastCoordinatesElementId = event.target.id;

                $this.data.cells.push({
                    cellId: event.target.id,
                    phaseId: $('#' + event.target.id).data('phase-id'),
                    accountId: $('#' + event.target.id).data('account-id'),
                    cellValue: $('#' + event.target.id).val()
                });
            }

            if ($this.changesCounter) {
                $('#' + $this.changesCounterId).html('...changes ready for calculation <b>' + $this.changesCounter + '</b>').show();
            } else {
                $('#' + $this.changesCounterId).html('').hide();
            }

            if ($this.debug) {
                console.log('collectData', $this.data);
            }
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
            }, 2000);
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
                $this.elementPercentagesTablePlace.html(data.html);

                if ($this.lastCoordinatesElementId) {
                    $('#' + $this.lastCoordinatesElementId).focus();
                }

                $this.pfpFunctions.tableStickyHeader();
                $this.pfpFunctions.tableStickyFirstColumn();
            }
        }
    }

    if ($('#percentagesTablePlace').length) {
        let PercentagesCalculatorClass = new PercentagesCalculator(new pfpFunctions());
        PercentagesCalculatorClass.init();
    }
});
