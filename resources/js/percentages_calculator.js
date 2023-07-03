import {calculatorCore} from "./calculator_core";

$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class PercentagesCalculator extends calculatorCore {
        constructor() {
            super();

            this.ajaxUrl = window.percentagesControllerUpdate;
            this.elementTablePlace = $('#percentagesTablePlace');

            this.autoSubmitDataDelay = $.cookie('percentage_autoSubmitDataDelay') !== undefined
                ? parseInt($.cookie('percentage_autoSubmitDataDelay'))
                : this.autoSubmitDataDelayDefault;

            this.autoSubmitDataAllow = $.cookie('percentage_autoSubmitDataAllow') !== undefined
                ? $.cookie('percentage_autoSubmitDataAllow')
                : this.autoSubmitDataAllowDefault;

            this.timeOutSeconds = 1000 * parseInt(this.autoSubmitDataDelay);
        }

        events() {
            let $this = this;
            super.events();

            $(document).on('change', 'input.percentage-value', function (event) {
                $this.loadData(event);
                $this.progressBar();
            });
        }

        collectData(event) {
            let $this = this;

            $this.changesCounter++;

            $this.data.businessId = window.percentagesBusinessId;
            $this.data.seatsCount = window.seatsCount;
            
            if (event && typeof event.target.id === 'string') {

                $this.lastCoordinatesElementId = event.target.id;
                $this.windowCoordinates = {
                    top: $(window).scrollTop(),
                    left: $(window).scrollLeft()
                };

                $this.data.cells.push({
                    cellId: event.target.id,
                    phaseId: $('#' + event.target.id).data('phase-id'),
                    accountId: $('#' + event.target.id).data('account-id'),
                    cellValue: $('#' + event.target.id).val()
                });
            }

            if ($this.changesCounter) {
                $('#' + $this.changesCounterId).html('...changes ready for calculation: <b>' + $this.changesCounter + '</b>'
                    + '<br/>' + $this.renderButtonForManualSubmit()).show();
            } else {
                $('#' + $this.changesCounterId).html('').hide();
            }

            if ($this.debug) {
                console.log('collectData', $this.data);
            }
        }

        updateSubmitDataSwitcher() {
            let $this = this;

            $.cookie('percentage_autoSubmitDataSwitcher', $this.autoSubmitDataSwitcher, {expires: 14});
        }

        updateSubmitDataDelay() {
            let $this = this;

            $.cookie('percentage_autoSubmitDataDelay', $this.autoSubmitDataDelay, {expires: 14});
        }

        updateAutoSubmitDataStatus() {
            super.updateAutoSubmitDataStatus();

            let $this = this;

            $.cookie('percentage_autoSubmitDataAllow', $this.autoSubmitDataAllow, {expires: 14});
        }

        afterLoadingDataHook() {
            let $this = this;

            $this.dragAdnDropValues();
        }

    }

    if ($('#percentagesTablePlace').length) {
        let PercentagesCalculatorClass = new PercentagesCalculator();
        PercentagesCalculatorClass.init();
    }


          
    $(document).on('click', 'input.percentage-value', function (event) {
        if($(this).val()==0)
        {
            $(this).val("");
        }
        
    });


    $(document).on('keypress', 'input.percentage-value', function (event) {

        
        // console.log(convertToASCII($(this).val()));
        // if(typeof($(this).val()) === 'string') 
        // {
        //     $(this).val("");
        // }

        if($(this).val()==0)
        {
            $(this).val("");
        }
        
    });

      $(document).on('blur', 'input.percentage-value', function (event) {
        if($(this).val()=="")
        {
            $(this).val(0);
        }
        
    });


});
