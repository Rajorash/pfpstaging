import {calculatorCore} from "./calculator_core";

$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class ProjectionsCalculator extends calculatorCore {
        constructor() {
            super();

            this.ajaxUrl = window.projectionsControllerUpdate;
            this.elementTablePlace = $('#projectionsTablePlace');

        }

        events() {
            let $this = this;

            $(document).on('change', '#currentProjectionsRange', function (event) {
                $this.loadData(event);
            });
        }

        collectData(event) {
            let $this = this;

            $this.changesCounter++;

            $this.windowCoordinates = {
                top: $(window).scrollTop(),
                left: $(window).scrollLeft()
            };

            $this.data.businessId = $('#businessId').val();
            $this.data.rangeValue = $('#currentProjectionsRange').val();

            if ($this.debug) {
                console.log('collectData', $this.data);
            }
        }
    }

    if ($('#projectionsTablePlace').length) {
        let ProjectionsCalculatorClass = new ProjectionsCalculator();
        ProjectionsCalculatorClass.init();
    }
});
