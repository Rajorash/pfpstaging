$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class AllocationCalculator {
        constructor() {
            this.ajaxUrl = window.allocationsControllerUpdate;

            this.resetData();
        }

        init() {
            let $this = this;

            $this.events();
        }

        resetData() {
            let $this = this;

            $this.data = {};
        }

        collectData() {
            let $this = this;

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

            console.log('collectData', $this.data);

            $.ajax({
                type: 'POST',
                url: $this.ajaxUrl,
                data: $this.data,
                success: function (data) {
                    console.log('loadData', data);
                }
            });

        }
    }

    let AllocationCalculatorClass = new AllocationCalculator();
    AllocationCalculatorClass.init();
});
