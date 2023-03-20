<x-app-layout>
  <x-slot name="$titleHeader">
    {{ __('Graph') }}
  </x-slot>

  <x-slot name="header">
    {{ __('Graph') }}
  </x-slot>

  <x-ui.main>
    <input type="hidden" id="businessId" name="businessId" value="{{$business->id}}"/>

  <script type="text/javascript">
        window.getGraphData = "{{route('getGraphData')}}";
  </script>
<style>
  .graph-child {
    width:49%;
    margin-bottom:40px;
  }
  .graph-child:nth-child(odd):last-child{
  width: 100%;
}
</style>
</head>

<div class="graph-container flex flex-wrap justify-between gap-2 p-5">
  <div class="graph-child ">
      <canvas id="myChart"></canvas>
  </div>
  <div class="graph-child ">
    <canvas id="myChart1"></canvas>
  </div>
  <div class="graph-child ">
    <canvas id="myChart2"></canvas>
  </div>
  <div class="graph-child  ">
    <canvas id="myChart3"></canvas>
  </div>

</div>



<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>

</x-ui.main>

<x-spinner-block/>

</x-app-layout>