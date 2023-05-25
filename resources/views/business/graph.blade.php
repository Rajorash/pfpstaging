<x-app-layout>
  <x-slot name="$titleHeader">
    {{ __('Graph') }}
  </x-slot>

  <x-slot name="header">
    {{ __('Graph') }}
  </x-slot>

  <x-slot name="header">
    <x-cta-workflow :business="$business" :step="'graph'" />
    {{$business->name }}
    <x-icons.chevron-right :class="'h-4 w-auto inline-block px-2'" />
    {{ __('Graph')}}
  </x-slot>

  <x-slot name="subMenu">
    <x-business-nav businessId="{{$business->id}}" :business="$business" />
  </x-slot>

  <x-ui.main>
    <input type="hidden" id="businessId" name="businessId" value="{{$business->id}}" />

    <script type="text/javascript">
      window.getGraphData = "{{route('getGraphData')}}";
      window.getTableData = "{{route('getTableData')}}";
    </script>
    <style>
      .graph-child {
        width: 49%;
        margin-bottom: 40px;
      }

      #rolling-sum-table table tr td{
        border-top: 1px solid #ddd;
      }
      #rolling-sum-table table tr:nth-child(odd) td{
        background-color: #f1f1f1;
      }

      .account-color {
        color: #7047e7;
      }
      /* .graph-child:nth-child(odd):last-child{
    width:100% !important;
    height:440px !important
  } */
    </style>
    </head>

    <div id="rolling-sum-table">
      <x-ui.table-table class="relative mb-2 cursor-fill-data">
        <thead>
          <tr class="border-b divide-x border-light_blue">
            <x-ui.table-th class="sticky top-0 left-0 text-center" baseClass="min-w-24 w-32 text-dark_gray font-normal bg-data-entry z-30">
              <span id="processCounter" class="hidden text-xs font-normal opacity-50"></span>
            </x-ui.table-th>
            @foreach($months as $date)
            <x-ui.table-th class="text-left text-dark_gray sticky top-0" baseClass="min-w-24 font-normal z-20"><span class="block text-xs font-normal"><b>{{ $date }}</b></span></x-ui.table-th>
            @endforeach
          </tr>
        </thead>
        <x-ui.table-tbody id="table-data" class=" text-xs font-normal">
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>Profit: </b></td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1400000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1000000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1234990</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">136789</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">900000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1287890</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">89000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1898709</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">19000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">15000</td>
          </tr>
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>Owners Pay: </b></td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">89000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1898709</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">19000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">15000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1400000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1000000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1234990</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">136789</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">900000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1287890</td>
          </tr>
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>Purchases: </b></td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1400000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1000000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1234990</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">136789</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">900000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1287890</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">89000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1898709</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">19000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">15000</td>
          </tr>
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>Opex: </b></td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">89000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1898709</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">19000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">15000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1400000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1000000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1234990</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">136789</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">900000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1287890</td>
          </tr>
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>Tax: </b></td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1400000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1000000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1234990</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">136789</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">900000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1287890</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">89000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1898709</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">19000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">15000</td>
          </tr>
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>G.S.T: </b></td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">89000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1898709</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">19000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">15000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1400000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1000000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1234990</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">136789</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">900000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1287890</td>
          </tr>
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>Promotions: </b></td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1400000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1000000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1234990</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">136789</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">900000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1287890</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">89000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1898709</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">19000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">15000</td>
          </tr>
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>Vehicles: </b></td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">89000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1898709</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">19000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">15000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1400000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1000000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1234990</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">136789</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">900000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1287890</td>
          </tr>
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>Provisions: </b></td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1400000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1000000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1234990</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">136789</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">900000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1287890</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">89000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1898709</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">19000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">15000</td>
          </tr>
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>Staff Account: </b></td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">89000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1898709</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">19000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">15000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1400000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1000000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1234990</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">136789</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">900000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1287890</td>
          </tr>
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>Vault-Rainy Day (in offset): </b></td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1400000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1000000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1234990</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">136789</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">900000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1287890</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">89000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1898709</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">19000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">15000</td>
          </tr>
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>Vault-Truck (in offset): </b></td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">89000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1898709</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">19000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">15000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1400000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1000000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">10000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1234990</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">136789</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">900000</td>
            <td class="px-2 py-4 min-w-24 text-xs font-normal">1287890</td>
          </tr>
        </x-ui.table-tbody>
      </x-ui.table-table>
    </div>

    <br>
    <hr>

    <div class="graph-container flex flex-wrap justify-between gap-2 p-5">
    </div>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>

    <script>
      // $(document).ready(function() {
      //   console.log("hello world");
      //   $.ajaxSetup({
      //     headers: {
      //       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      //     }
      //   });

      //   class Table {
      //     constructor(businessId) {
      //       this.businessId = $('#businessId').val();
      //       this.elementLoadingSpinner = $('#loadingSpinner');
      //       this.ajaxUrl2 = window.getTableData;
      //       this.debug = false;
      //       // $this.data.businessId = $('#businessId').val();
      //     }

      //     init() {
      //       let $this = this;
      //       $this.showSpinner();
      //       $this.hideSpinner();
      //       $this.renderData();
      //       $this.events();
      //     }

      //     events() {
      //       super.events();
      //       $this.ajaxTableLoadWorker();
      //     }

      //     showSpinner() {
      //       let $this = this;

      //       if ($this.debug) {
      //         console.log('showSpinner');
      //       }

      //       $('html, body').css({
      //         overflow: 'hidden',
      //         height: '100%'
      //       });

      //       $this.elementLoadingSpinner.show();
      //     }

      //     hideSpinner() {
      //       let $this = this;

      //       $('html, body').css({
      //         overflow: 'auto',
      //         height: 'auto'
      //       });

      //       if ($this.debug) {
      //         console.log('hideSpinner');
      //       }

      //       $this.elementLoadingSpinner.hide();
      //     }

      //     ajaxTableLoadWorker() {
      //       let $this = this;
      //       console.log($this.ajaxUrl2);
      //       $.ajax({
      //         type: 'POST',
      //         url: $this.ajaxUrl2,
      //         data: {
      //           'id': $('#businessId').val()
      //         },
      //         // dataType: 'json',
      //         // beforeSend: function() {
      //         //   $this.showSpinner();
      //         // },
      //         success: function(data) {
      //           // document.getElementById("rolling-sum-table").innerHTML = data;
      //         },
      //         // complete: function() {
      //         //   $this.hideSpinner();
      //         // }
      //       });
      //     }
      //   }

      //   let TableClass = new Table();
      //   TableClass.ajaxTableLoadWorker();
      // });
    </script>

  </x-ui.main>

  <x-spinner-block />

</x-app-layout>