$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class AllocationCalculator {
        constructor() {
            this.debug = true;

            this.ajaxUrl = window.allocationsControllerUpdate;
            this.elementAllocationTablePlace = $('#allocationTablePlace');
            this.elementLoadingSpinner = $('#loadingSpinner');

            this.changesCounter = 0;

            this.timeout;
        }

        init() {
            let $this = this;

            $this.resetData();
            $this.events();

            $this.loadData();
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

        collectData(cellId) {
            let $this = this;

            $this.changesCounter++;

            $this.data.startDate = $('#startDate').val();
            $this.data.rangeValue = $('#currentRangeValue').val();
            if (typeof cellId === 'string') {
                $this.data.cells.push({
                    cellId: cellId,
                    cellValue: $('#' + cellId).val()
                });
            }

            if ($this.changesCounter) {
                $('#processCounter').html('...changes ready for calculation <b>' + $this.changesCounter + '</b>').show();
            } else {
                $('#processCounter').html('').hide();
            }

            if ($this.debug) {
                console.log('collectData', $this.data);
            }
        }

        events() {
            let $this = this;

            $(document).on('change', '#startDate', $this.loadData.bind($this));
            $(document).on('change', '#currentRangeValue', $this.loadData.bind($this));
            $(document).on('change', '#allocationTablePlace input', function (event) {
                $this.loadData(event.target.id);
            });
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

        loadData(cellId) {
            let $this = this;

            $this.collectData(cellId);

            clearTimeout($this.timedOut);
            $this.timedOut = setTimeout(function () {
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
            }, 2000);
        }

        renderData(data) {
            let $this = this;

            if (data.error.length === 0) {
                $this.elementAllocationTablePlace.html(data.html);
            }
        }
    }

    let AllocationCalculatorClass = new AllocationCalculator();
    AllocationCalculatorClass.init();
});
