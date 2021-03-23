@php
foreach($dates as $date){
    echo $date->format('Y-m-d').' _ ';
}
echo '<br>';
@endphp
<tr>
    <td class="px-2 pb-1 whitespace-nowrap {{$flow->negative_flow ? 'bg-red-100' : 'bg-green-100' }}">
        {{ $flow->label }}
    </td>

    @foreach ($dates as $date)
    {{-- <td class="p-1 whitespace-nowrap {{$flow->negative_flow ? 'bg-red-100' : 'bg-green-100' }}">
        <x-ui.input class="text-right" placeholder="0" />
    </td> --}}
    <livewire:calculator.account-flow :flowId="$flow->id" :date="$date->format('Y-m-d')" :datesRange="$dates" key="flow-{{$date}}-{{$flow->id}}">
    @endforeach
</tr>
