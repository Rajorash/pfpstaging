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
        }

        init() {
            let $this = this;

            $this.resetData();
            $this.events();

            $this.loadData();
        }

        resetData() {
            let $this = this;

            $this.data = {};
        }

        collectData(cellId) {
            let $this = this;

            $this.resetData();

            $this.data.startDate = $('#startDate').val();
            $this.data.rangeValue = $('#currentRangeValue').val();
            if (typeof cellId === 'string') {
                $this.data.cellId = cellId;
                $this.data.cellValue = $('#' + cellId).val();
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

            console.log(cellId);

            $this.collectData(cellId);

            if ($this.debug) {
                console.log('collectData', $this.data);
            }

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
                }
            });
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
