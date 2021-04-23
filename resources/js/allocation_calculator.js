import {calculatorCore} from "./calculator_core";

$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class AllocationCalculator extends calculatorCore {
        constructor() {
            super();

            this.ajaxUrl = window.allocationsControllerUpdate;
            this.elementTablePlace = $('#allocationTablePlace');
        }


        events() {
            let $this = this;

            $(document).on('change', '#startDate, #currentRangeValue, #allocationTablePlace input', function (event) {
                $this.loadData(event);
            });
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
    }

    if ($('#allocationTablePlace').length) {
        let AllocationCalculatorClass = new AllocationCalculator();
        AllocationCalculatorClass.init();
    }
});
