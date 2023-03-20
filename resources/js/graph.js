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
            console.log(data);
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
                        console.log("data",data);
                            var dataArray=data.data;
                            
                            $(".business").append('<canvas id="myChart'+i+'" height="90"></canvas>');

                            var newDataArray=$this.makeArray(dataArray,i);

                              new Chart('myChart'+i, {
                                    type: 'line',
                                        data: {
                                        labels: createLabels(),
                                        datasets: [{
                                            label: 'Data',
                                            fill: false,
                                            data: newDataArray,
                                            borderColor: 'blue',
                                            borderWidth:1,
                                            pointRadius: 0,
                                        }]
                                        },
                                        options: {
                                        scales: {
                                            xAxes: [{
                                            type: 'time',
                                            time: {
                                                displayFormats: 
                                                {
                                                    quarter: 'MMM YYYY'
                                                }
                                            },
                                            ticks: {
                                                source: 'labels'
                                            },      
                                            }],
                                            yAxes: [{
                                            ticks: {
                                                beginAtZero: true
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
                                        plugins: {
                                            legend: {
                                                display: true,
                                                position:'bottom',
                                                labels: {
                                                    color: 'rgb(255, 99, 132)'
                                                }
                                            }
                                        }
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
    var formattedStartDate = new Date("2023-01-01");
    var formattedEndDate = new Date(formattedStartDate.getFullYear(), 11, 31);

    while (formattedStartDate < formattedEndDate) {
        labels.push(formattedStartDate.toISOString().substring(0, 10));
        formattedStartDate.setMonth(formattedStartDate.getMonth() + 3);
    }
    return labels;
    }

    

    let GraphClass = new Graph();
    GraphClass.ajaxGraphLoadWorker();

});
