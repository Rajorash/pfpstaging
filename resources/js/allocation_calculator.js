$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class AllocationCalculator {
        constructor() {
            this.ajaxUrl = window.allocationsControllerUpdate;
        }

        init() {
            let $this = this;

            console.log($this.ajaxUrl);

            $this.events();
        }

        collectData() {
            let $this = this;
        }

        events() {
            let $this = this;

            document.on('change', '#startdate', $this.reloadPage.bind($this));
        }

        reloadPage() {
        }
    }

    let AllocationCalculatorClass = new AllocationCalculator();
    AllocationCalculatorClass.init();
});
