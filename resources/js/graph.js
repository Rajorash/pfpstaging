$(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    class Graph  {
        constructor(businessId) {
            this.businessId = $('#businessId').val();
            this.elementLoadingSpinner = $('#loadingSpinner');
            this.ajaxUrl = window.getGraphData;
            this.debug = false;
            // $this.data.businessId = $('#businessId').val();
        }

        init() {
            let $this = this;
            $this.showSpinner();
            $this.hideSpinner();
            $this.renderData();
            $this.events();
        }

        events() {
            super.events();
            $this.ajaxGraphLoadWorker();
        }

        showSpinner() {
            let $this = this;
    
            if ($this.debug) {
                console.log('showSpinner');
            }
    
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
    
            if ($this.debug) {
                console.log('hideSpinner');
            }
    
            $this.elementLoadingSpinner.hide();
        }

        makeArray(data,i){
            var newDataArr=[]
            for (var key in data[i].dates) {
                var dataSet={};
                if (data[i].dates.hasOwnProperty(key)) {
                    dataSet.x=key;
                    dataSet.y=Math.round(data[i].dates[key]);
                    newDataArr.push(dataSet);
                }
            }
            return newDataArr;
        }

        
        renderData(data) {
            let $this = this;
            if ($this.debug) {
                console.log('renderData',data);
            }
            // if (data.error.length === 0) {
            //     $this.elementTablePlace.html(data.html);

            //     if ($this.lastCoordinatesElementId) {
            //         $('#' + $this.lastCoordinatesElementId).focus();
            //         $('#' + $this.lastCoordinatesElementId).select();
            //     }

            //     $this.pfpFunctions.tableStickyHeader();
            //     $this.pfpFunctions.tableStickyFirstColumn();
            // } else {
            //     $this.elementTablePlace.html('<p class="p-8 text-red-700 text-bold">' + data.error.join('<br/>') + '</p>');
            // }
        }

        ajaxGraphLoadWorker() {
            let $this = this;
            $.ajax({
                type: 'POST',
                url: $this.ajaxUrl,
                data: {'id':$('#businessId').val()},
                dataType : 'json',
                beforeSend: function () {
                    $this.showSpinner();
                },
                success: function (data) {
                    for(var i=0; i<data.data.length; i++){
                            var dataArray=data.data;
                            $(".graph-container").append('<div class="graph-child "><canvas id="myChart'+i+'" style="height:370px;"></canvas></div>');
                            var newDataArray=$this.makeArray(dataArray,i);
                            new Chart('myChart'+i, {
                                    type: 'line',
                                        data: {
                                        labels: createLabels(),
                                        datasets: [{
                                            label: data.data[i].name,
                                            fill: false,
                                            data: newDataArray,
                                            borderColor: 'blue',
                                            borderWidth:1,
                                            pointRadius: 0,
                                        }]
                                        },
                                        options: {
                                        legend: {
                                            position: 'bottom',
                                            labels: {
                                                fontSize: 18
                                            },
                                        },
                                        scales: {
                                            xAxes: [{
                                            type: 'time',
                                            time: {
                                                unit: 'month'
                                            },
                                            ticks: {
                                                autoSkip: false,
                                                maxRotation: 0,
                                                minRotation: 0,
                                                source: 'labels'
                                            },      
                                            }],
                                            yAxes: [{
                                            ticks: {
                                                beginAtZero: true,
                                            }
                                            }],
                                            y: {
                                                stacked: true
                                            }
                                        },
                                        tooltips: {
                                            mode: 'index'
                                        },
                                        hover: {
                                            mode: 'index',
                                            intersect: false
                                        },
                                        }
                            });
                    }
                },
                complete: function () {
                    $this.hideSpinner();
                }
            });
        }
    }

    const labels = [];

    //Creating labels here for graph
    function createLabels() {
        var todayDate = new Date().toISOString().slice(0, 10);
        var firstMonthDate = new Date(todayDate);
        labels.push(firstMonthDate);
        firstMonthDate.setMonth(firstMonthDate.getMonth() +4);
        var secondMonthDate=firstMonthDate.toISOString().slice(0, 10);
        labels.push(secondMonthDate);
        firstMonthDate.setMonth(firstMonthDate.getMonth() +5);
        var thirdMonthDate=firstMonthDate.toISOString().slice(0, 10);
        labels.push(thirdMonthDate);
        firstMonthDate.setMonth(firstMonthDate.getMonth() +4);
        var fourthMonthDate=firstMonthDate.toISOString().slice(0, 10);
        labels.push(fourthMonthDate);
    return labels;
    }

    

    let GraphClass = new Graph();
    GraphClass.ajaxGraphLoadWorker();

});
