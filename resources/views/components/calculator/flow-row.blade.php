@props(['flow', 'dates'])
<tr>
    <td class="px-2 pb-1 whitespace-nowrap {{$flow->negative_flow ? 'bg-red-100' : 'bg-green-100' }}">
        {{ $flow->label }}
    </td>
    @foreach ($dates as $date)
    <td class="p-1 whitespace-nowrap {{$flow->negative_flow ? 'bg-red-100' : 'bg-green-100' }}">
        <x-ui.input class="text-right" placeholder="0" />
    </td>
    @endforeach
</tr>
