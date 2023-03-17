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

</head>

<div class="row">
  <div class="col-md-6">
      <canvas id="myChart" height="90"></canvas>
  </div>
  <div class="col-md-6">
    <canvas id="myChart1" height="90"></canvas>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
      <canvas id="myChart2" height="90"></canvas>
  </div>
  <div class="col-md-6">
    <canvas id="myChart3" height="90"></canvas>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>

</x-ui.main>

<x-spinner-block/>

</x-app-layout>