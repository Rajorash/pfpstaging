<x-app-layout>

  <x-slot name="$titleHeader">
    {{ __('Graph') }}
  </x-slot>

  <x-slot name="header">
    {{ __('Graph') }}
  </x-slot>

  <x-ui.main>


    <!-- Required chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          fontFamily: {
            sans: ["Roboto", "sans-serif"],
            body: ["Roboto", "sans-serif"],
            mono: ["ui-monospace", "monospace"],
          },
        },
      };
    </script>





    <div class="overflow-hidden rounded-lg shadow-lg">
      <!-- <div class="bg-neutral-50 py-3 px-5 dark:bg-neutral-700 dark:text-neutral-200">
      Line chart
    </div> -->
      <div class="chart">

        <canvas class="p-10" id="materials_bank_account" width="400" height="150" aria-label="Materials Bank Account" role="img"></canvas>
        <canvas class="p-10" id="commissions_bank_account" width="400" height="150" aria-label="Commissions Bank Account" role="img"></canvas>
        <canvas class="p-10" id="profit" width="400" height="150" aria-label="Profit" role="img"></canvas>
        <canvas class="p-10" id="ownerspay" width="400" height="150" aria-label="Owners Pay" role="img"></canvas>
        <canvas class="p-10" id="opex_bank_account" width="400" height="150" aria-label="Opex Bank Account" role="img"></canvas>
        <canvas class="p-10" id="tax_bank_account" width="400" height="150" aria-label="Tax Bank Account" role="img"></canvas>
        <canvas class="p-10" id="gst_bank_account" width="400" height="150" aria-label="GST Bank Account" role="img"></canvas>

      </div>


    </div>




    <script>

      /****************** Materials Bank Account Start ******************/
      const labels_materials_bank = [
        "12-01-2022",
        "24-01-2022",
        "05-02-2022",
        "17-02-2022",
        "01-03-2022",
        "13-03-2022",
        "25-03-2022",
        "06-04-2022",
        "18-04-2022",
        "30-04-2022",
        "12-05-2022",
        "24-05-2022",
        "05-06-2022",
        "17-06-2022",
        "29-06-2022",
        "11-07-2022",
        "23-07-2022",
        "04-08-2022",
        "16-08-2022",
        "28-08-2022",
        "09-09-2022",
        "21-09-2022",
        "03-10-2022",
        "15-10-2022",
        "27-10-2022",
        "08-10-2022",
        "20-11-2022",
        "02-12-2022",
        "14-12-2022",
        "26-12-2022",
      ];
      const data_materials_bank = {
        labels: labels_materials_bank,
        datasets: [{
          label: "Materials",

          backgroundColor: "orange",
          borderColor: "hsl(217, 57%, 51%)",
          data: [

            0,
            1000000,
            2000000,
            3000000,
            1000000,
            4000000,
            5000000,
            2000000,
            6000000,

            7000000,
            8000000,
            9000000,
            10000000,
            500000
          ],
        }, ],
      };

      const verticalLine = {
        id: 'verticalLine',
        beforeDraw(chart, args, options) {
          const {
            ctx,
            chartArea: {
              top,
              right,
              bottom,
              left,
              width,
              height
            },
            scales: {
              x,
              y
            },
          } = chart
          ctx.save()

          // draw line
          ctx.strokeStyle = options.lineColor
          // x0 : starting point on the horizontal line. Left to Right
          // y0 : starting point on the vertical line. Top to Bottom
          // x1 : length point on the horizontal line. Left to Right
          // y1 : length point on the vertical line. Top to Bottom
          ctx.strokeRect(left, y.getPixelForValue(options.yPosition), width, 1)

          ctx.restore()
        },
      }

      const config_materials_bank = {
        type: "line",
        data: data_materials_bank,

        options: {
          // responsive:false,
          plugins: {

            title: {
              display: true,
              text: 'Materials Bank Account',

              font: {
                size: 24
              },

            },
            legend: {
              display: true,
              position: "bottom"
            },
            verticalLine: {
              lineColor: 'orange',
              yPosition: 5000000,


            },
          },
          scales: {
            x: {
              title: {
                color: 'red',
                display: true,
                text: 'Peace of Mind',

                align: "start"
              },
              ticks: {
                autoSkip: false,
                maxRotation: 90,
                minRotation: 90
              }
            }
          }
        },
        plugins: [verticalLine],
      }


      var materials_bank_account = new Chart(
        document.getElementById("materials_bank_account"),
        config_materials_bank
      );

         /****************** Materials Bank Account End******************/


      const verticalLineSecond = {
        id: 'verticalLineSecond',
        beforeDraw(chart, args, options) {
          const {
            ctx,
            chartArea: {
              top,
              right,
              bottom,
              left,
              width,
              height
            },
            scales: {
              x,
              y
            },
          } = chart
          ctx.save()

          // draw line
          ctx.strokeStyle = options.lineColor
          // x0 : starting point on the horizontal line. Left to Right
          // y0 : starting point on the vertical line. Top to Bottom
          // x1 : length point on the horizontal line. Left to Right
          // y1 : length point on the vertical line. Top to Bottom
          ctx.strokeRect(left, y.getPixelForValue(options.yPosition), width, 1)

          ctx.restore()
        },
      }



      /****************** Commissions Bank Account Start ******************/

      const labels_commissions_bank = [
        "12-01-2022",
        "24-01-2022",
        "05-02-2022",
        "17-02-2022",
        "01-03-2022",
        "13-03-2022",
        "25-03-2022",
        "06-04-2022",
        "18-04-2022",
        "30-04-2022",
        "12-05-2022",
        "24-05-2022",
        "05-06-2022",
        "17-06-2022",
        "29-06-2022",
        "11-07-2022",
        "23-07-2022",
        "04-08-2022",
        "16-08-2022",
        "28-08-2022",
        "09-09-2022",
        "21-09-2022",
        "03-10-2022",
        "15-10-2022",
        "27-10-2022",
        "08-10-2022",
        "20-11-2022",
        "02-12-2022",
        "14-12-2022",
        "26-12-2022",
      ];

      const data_commissions_bank = {
        labels: labels_commissions_bank,
        datasets: [{
          label: "Commissions",

          backgroundColor: "orange",
          borderColor: "hsl(217, 57%, 51%)",
          data: [

            0,
            200000,
            400300,
            600400,
            800500,
            9030000,
          ],
        }, ],
      };

      const config_commissions_bank = {
        type: "line",
        data: data_commissions_bank,

        options: {
          // responsive:false,
      
   
          plugins: {

            title: {
              display: true,
              text: 'Commissions Bank Account',

              font: {
                size: 24
              },

            },
            subtitle: {
                display: true,
                text: 'Custom Chart Subtitle',
                position:"bottom"
            },
            legend: {
              display: true,
              position: "bottom"
            },
            verticalLine: {
              lineColor: 'orange',
              yPosition: 7800000,


            },
          },
          scales: {
            x: {
              title: {
                color: 'red',
                display: true,
                text: 'Peace of Mind',

                align: "start",

              },
              ticks: {
                autoSkip: false,
                maxRotation: 90,
                minRotation: 90
              }
            }
          }
        },
        plugins: [verticalLine],
      }

      var commissions_bank_account = new Chart(
        document.getElementById("commissions_bank_account"),
        config_commissions_bank
      );

  /****************** Commissions Bank Account End ******************/



  /****************** Profit Bank Start ****************************/


  const labels_profit_bank = [

        "12-01-2022",
        "24-01-2022",
        "05-02-2022",
        "17-02-2022",
        "01-03-2022",
        "13-03-2022",
        "25-03-2022",
        "06-04-2022",
        "18-04-2022",
        "30-04-2022",
        "12-05-2022",
        "24-05-2022",
        "05-06-2022",
        "17-06-2022",
        "29-06-2022",
        "11-07-2022",
        "23-07-2022",
        "04-08-2022",
        "16-08-2022",
        "28-08-2022",
        "09-09-2022",
        "21-09-2022",
        "03-10-2022",
        "15-10-2022",
        "27-10-2022",
        "08-10-2022",
        "20-11-2022",
        "02-12-2022",
        "14-12-2022",
        "26-12-2022",
      ];
      const data_profit_bank = {
        labels: labels_profit_bank,
        datasets: [{
          label: "Profit",

          backgroundColor: "orange",
          borderColor: "hsl(217, 57%, 51%)",
          data: [

            0,
            100000,
            200300,
            300400,
            400500,
            500400,
            644000,
            766000,
            856000,

          ],
        }, ],
      };

      const config_profit_bank = {
        type: "line",
        data: data_profit_bank,

        options: {
          // responsive:false,
          plugins: {

            title: {
              display: true,
              text: 'Profit',

              font: {
                size: 24
              },

            },
            legend: {
              display: true,
              position: "bottom"
            },
            // verticalLine: {
            //   lineColor: 'orange',
            //   yPosition: 5000000,
            // },
          },
          scales: {
            x: {
              title: {
                color: 'red',
                display: true,
                text: 'Peace of Mind',

                align: "start"
              },
              ticks: {
                autoSkip: false,
                maxRotation: 90,
                minRotation: 90
              }
            }
          }
        },
        plugins: [verticalLine],
      }

      var profit = new Chart(
        document.getElementById("profit"),
        config_profit_bank
      );

  /****************** Profit Bank End ****************************/


 /****************** Owners Pay Start ****************************/

      const labels_owners_pay_bank = [
        1, 14, 27, 40, 53, 66, 79, 92, 105, 118, 131, 144, 157, 170, 183, 196, 209, 222, 235,
        248, 300, 313, 326, 339, 352, 365
      ];

      const data_owners_pay_bank = {
        labels: labels_owners_pay_bank,
        datasets: [{
          label: "Peace of Mind 0",

          backgroundColor: "blue",
          borderColor: "orange",
          data: [
            11,
            08,
            06,
            03,
            01,
            30,
            26,
            23,
            21,
            18,
            14,
            11,
            08,
            06,
            03,
            01,

            // '30-07-2447',
            // '26-10-2392',
            // '23-01-2338',
            // '21-04-2238',
            // '18-07-2228',
            // '14-10-2119',
            // '11-01-2119',
            // '08-04-2064',
            // '06-07-2009',
            // '03-10-1954',
            // '01-01-1900',

          ],
        }, ],
      };

      const config_owners_pay_bank = {
        type: "line",
        data: data_owners_pay_bank,

        options: {
          // responsive:false,
          plugins: {

            title: {
              display: true,
              text: 'Owners Pay',

              font: {
                size: 24
              },

            },
            legend: {
              display: true,
              position: "bottom"
            },
            verticalLine: {
              lineColor: 'blue',
              yPosition: 9,



            },
            verticalLineSecond: {
              lineColor: 'grey',
              yPosition: 7,


            },
          },
          scales: {
            x: {
              title: {
                color: 'red',
                display: true,
                text: 'Peace of Mind',

                align: "start",

              },
              ticks: {
                autoSkip: false,
                maxRotation: 90,
                minRotation: 90
              }
            }
          }
        },
        plugins: [verticalLine, verticalLineSecond],
      }


      var ownerspay = new Chart(
        document.getElementById("ownerspay"),
        config_owners_pay_bank
      );
 /****************** Owners Pay End ****************************/



/**************** Opex Bank Account Start ******************/


      const labels_opex_bank = [
        "12-01-2022",
        "24-01-2022",
        "05-02-2022",
        "17-02-2022",
        "01-03-2022",
        "13-03-2022",
        "25-03-2022",
        "06-04-2022",
        "18-04-2022",
        "30-04-2022",

        "12-05-2022",
        "24-05-2022",
        "05-06-2022",
        "17-06-2022",

        "29-06-2022",
        "11-07-2022",
        "23-07-2022",
        "04-08-2022",

        "16-08-2022",
        "28-08-2022",
        "09-09-2022",

        "21-09-2022",
        "03-10-2022",
        "15-10-2022",

        "27-10-2022",
        "08-10-2022",
        "20-11-2022",


        "02-12-2022",
        "14-12-2022",
        "26-12-2022",
      ];
      const data_opex_bank = {
        labels: labels_opex_bank,
        datasets: [{
          label: "Opex Bank A/C",
          backgroundColor: "orange",
          borderColor: "hsl(217, 57%, 51%)",
          data: [

            0,
            500000,
            600000,
            660000,
            700000,
            1000000,
            2000020,
            3000000,
            3400000
          ],
        }, ],
      };



      const config_opex_bank = {
        type: "line",
        data: data_opex_bank,

        options: {
          // responsive:false,
          plugins: {

            title: {
              display: true,
              text: 'Opex Bank Account',

              font: {
                size: 24
              },

            },
            legend: {
              display: true,
              position: "bottom"
            },
            verticalLine: {
              lineColor: 'orange',
              yPosition: 2000000,



            },

          },
          scales: {
            x: {
              title: {
                color: 'red',
                display: true,
                text: 'Peace of Mind',

                align: "start",

              },
              ticks: {
                autoSkip: false,
                maxRotation: 90,
                minRotation: 90
              }
            }
          }
        },
        plugins: [verticalLine],
      }

      var opex_bank_account = new Chart(
        document.getElementById("opex_bank_account"),
        config_opex_bank
      );

/**************** Opex Bank Account End ******************/


/*********************Tax Bank Account Start******************/


      const labels_tax_bank = [
        "12-01-2022",
        "24-01-2022",
        "05-02-2022",
        "17-02-2022",
        "01-03-2022",
        "13-03-2022",
        "25-03-2022",
        "06-04-2022",
        "18-04-2022",
        "30-04-2022",

        "12-05-2022",
        "24-05-2022",
        "05-06-2022",
        "17-06-2022",

        "29-06-2022",
        "11-07-2022",
        "23-07-2022",
        "04-08-2022",

        "16-08-2022",
        "28-08-2022",
        "09-09-2022",

        "21-09-2022",
        "03-10-2022",
        "15-10-2022",

        "27-10-2022",
        "08-10-2022",
        "20-11-2022",


        "02-12-2022",
        "14-12-2022",
        "26-12-2022",
      ];
      const data_tax_bank_bank = {
        labels: labels_tax_bank,
        datasets: [{
          label: "Tax",

          backgroundColor: "orange",
          borderColor: "hsl(217, 57%, 51%)",
          data: [

            0,

            100000,
            200300,
            300400,
            400500,
            500400,
            644000,
            766000,


          ],
        }, ],
      };



      const config_tax_bank = {
        type: "line",
        data: data_tax_bank_bank,

        options: {
          // responsive:false,
          plugins: {

            title: {
              display: true,
              text: 'Tax Bank Account',

              font: {
                size: 24
              },

            },
            legend: {
              display: true,
              position: "bottom"
            },
            verticalLine: {
              lineColor: 'orange',
              yPosition: 200000,



            },

          },
          scales: {
            x: {
              title: {
                color: 'red',
                display: true,
                text: 'Peace of Mind',

                align: "start",

              },
              ticks: {
                autoSkip: false,
                maxRotation: 90,
                minRotation: 90
              }
            }
          }
        },
        plugins: [verticalLine],
      }

      var tax_bank_account = new Chart(
        document.getElementById("tax_bank_account"),
        config_tax_bank
      );


/*********************Tax Bank Account End******************/

/****************GST Bank Account Start**************************/


      const labels_gst_bank = [
        "12-01-2022",
        "24-01-2022",
        "05-02-2022",
        "17-02-2022",
        "01-03-2022",
        "13-03-2022",
        "25-03-2022",
        "06-04-2022",
        "18-04-2022",
        "30-04-2022",

        "12-05-2022",
        "24-05-2022",
        "05-06-2022",
        "17-06-2022",

        "29-06-2022",
        "11-07-2022",
        "23-07-2022",
        "04-08-2022",

        "16-08-2022",
        "28-08-2022",
        "09-09-2022",

        "21-09-2022",
        "03-10-2022",
        "15-10-2022",

        "27-10-2022",
        "08-10-2022",
        "20-11-2022",


        "02-12-2022",
        "14-12-2022",
        "26-12-2022",
      ];
      const data_gst_bank = {
        labels: labels_gst_bank,
        datasets: [{
          label: "GST",
          backgroundColor: "orange",
          borderColor: "hsl(217, 57%, 51%)",
          data: [

            0,
            10000,
            15000,
            20000,
            40000,
            80000,
            400300,
            600400,
            800500,
            1000400,
            2404000,
            7366000,
            8000000,

          ],
        }, 
      

       
      ],
      };



      const config_gst_bank = {
        type: "line",
        data: data_gst_bank,

        options: {
          // responsive:false,
          plugins: {

            title: {
              display: true,
              text: 'GST Bank Account',

              font: {
                size: 24
              },

            },
            legend: {
              display: true,
              position: "bottom"
            },
            verticalLine: {
              lineColor: 'orange',
              yPosition: 80000,
            },

          },
          scales: {
            x: {
              title: {
                color: 'red',
                display: true,
                text: 'Peace of Mind',

                align: "start",

              },
              ticks: {
                autoSkip: false,
                maxRotation: 90,
                minRotation: 90
              }
            }
          }
        },
        plugins: [verticalLine],
      }

      var gst_bank_account = new Chart(
        document.getElementById("gst_bank_account"),
        config_gst_bank
      );


      
/****************GST Bank Account End**************************/


    </script>
    <script src="https://cdn.jsdelivr.net/npm/tw-elements/dist/js/index.min.js"></script>



  </x-ui.main>

</x-app-layout>