@php
    $links = collect([
        "/business/${businessId}/accounts" => "Accounts",
        "/allocations/${businessId}" => "Allocations",
        "/allocations/${businessId}/percentages" => "Percentages",
        "/projections/${businessId}" => "Projections",
    ]);
@endphp

<div class="container secondary-nav nav mt-n3 mb-2">
    @foreach ($links as $link => $label)

    @if ($loop->first)
    <a class="ml-auto mr-2" href="{{$link}}">{{$label}}</a> |
    @elseif (!$loop->last)
    <a class="mx-2" href="{{$link}}">{{$label}}</a> |
    @else
    <a class="ml-2" href="{{$link}}">{{$label}}</a>
    @endif

    @endforeach
</div>
