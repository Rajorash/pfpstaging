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

        collectData() {
            let $this = this;

            $this.resetData();

            $this.data.startDate = $('#startDate').val();
            $this.data.rangeValue = $('#currentRangeValue').val();
        }

        events() {
            let $this = this;

            $(document).on('change', '#startDate', $this.loadData.bind($this));
            $(document).on('change', '#currentRangeValue', $this.loadData.bind($this));
        }

        loadData() {
            let $this = this;

            $this.collectData();

            if ($this.debug) {
                console.log('collectData', $this.data);
            }

            $.ajax({
                type: 'POST',
                url: $this.ajaxUrl,
                data: $this.data,
                success: function (data) {
                    if ($this.debug) {
                        console.log('loadData', data);
                    }
                    $this.renderData(data);
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
