@props(['route', 'title','linkTitle'])
<div class="mb-2">
    {{$icon}}
</div>

<div class="text-lg text-dark_gray2 leading-7">
    <a href="{{$route}}">{{$title}}</a>
</div>

<div class="mt-2 text-sm">
    {{$slot}}
</div>

<a href="{{ $route}}">
    <div class="mt-3 flex items-center text-sm font-normal text-blue flex justify-start">
        <div>{{$linkTitle}}</div>
        <div class="ml-2 text-blue">
            <x-icons.arrow-right/>
        </div>
    </div>
</a>
