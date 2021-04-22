$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class AllocationCalculator {
        constructor() {
            this.debug = false;

            this.ajaxUrl = window.allocationsControllerUpdate;
            this.elementAllocationTablePlace = $('#allocationTablePlace');
            this.elementLoadingSpinner = $('#loadingSpinner');

            this.changesCounter = 0;
            this.changesCounterId = 'processCounter';
            this.lastCoordinatesElementId = '';

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

            $(document).on('change', '#startDate, #currentRangeValue, #allocationTablePlace input', function (event) {
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

            $this.data.businessId = $('#businessId').val();
            $this.data.startDate = $('#startDate').val();
            $this.data.rangeValue = $('#currentRangeValue').val();

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

        tableStickyHeader() {
            if ($('.table-sticky-header').length) {
                $('.table-sticky-header').floatThead({
                    position: 'absolute'
                });
            }
        }

        tableStickyFirstColumn() {
            if ($('.table-sticky-first-column').length) {
                $('.table-sticky-first-column').stickyColumn({columns: 1});
            }
        }

        renderData(data) {
            let $this = this;

            if (data.error.length === 0) {
                $this.elementAllocationTablePlace.html(data.html);

                if ($this.lastCoordinatesElementId) {
                    $('#' + $this.lastCoordinatesElementId).focus();
                }

                $this.tableStickyHeader();
                // $this.tableStickyFirstColumn();
            }
        }
    }

    if ($('#allocationTablePlace').length) {
        let AllocationCalculatorClass = new AllocationCalculator();
        AllocationCalculatorClass.init();
    }
});
