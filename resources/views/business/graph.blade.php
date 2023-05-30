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
    </script>
    <style>
      .graph-child {
        width: 49%;
        margin-bottom: 40px;
      }

      #rolling-sum-table table tr td {
        border-top: 1px solid #ddd;
      }

      #rolling-sum-table table tr:nth-child(odd) td {
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

    <div id="rolling-sum-table" style="display: none;">
      <x-ui.table-table class="relative mb-2 cursor-fill-data">
        <thead>
          <tr class="border-b divide-x border-light_blue">
            <x-ui.table-th class="sticky top-0 left-0 text-center" baseClass="min-w-24 w-32 text-dark_gray font-normal bg-data-entry z-30">
              <span id="processCounter" class="hidden text-xs font-normal opacity-50"></span>
            </x-ui.table-th>
            @foreach($months as $date)
            <x-ui.table-th class="text-left text-dark_gray sticky top-0" baseClass="min-w-24 font-normal z-20"><span class="block text-xs font-normal"><b>{{ $date['name'] }}</b></span></x-ui.table-th>
            @endforeach
          </tr>
        </thead>
        <x-ui.table-tbody id="table-data" class="text-xs font-normal">
          @foreach($calculations as $data)
          <tr>
            <td class="px-2 py-4 min-w-24 text-xs font-normal account-color"><b>{{ $data['name']; }}: </b></td>
            @foreach($months as $month)
            <td class="px-2 py-4 min-w-24 text-xs font-normal">{{ round($data[$month['name']]) }}</td>
            @endforeach
          </tr>
          @endforeach
        </x-ui.table-tbody>
      </x-ui.table-table>
    </div>

    <br>
    <hr>

    <div class="graph-container flex flex-wrap justify-between gap-2 p-5">
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>

    <script>
      $(document).ready(function() {
        setTimeout(function() {
          $("#rolling-sum-table").show();
        }, 10000);
      });
    </script>

  </x-ui.main>

  <x-spinner-block />

</x-app-layout>