@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"><strong>Select A Business To See It's Allocations</strong></div>

                <div class="card-body p-0">
                <table class="table table-striped">
                    <thead class="thead-inverse">
                        <tr>
                            <th>Business Name</th>
                            <th>Owner</th>
                            <th>Advisor</th>
                            <th>Accounts</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($businesses as $business)
                        <tr>
                            <td scope="row">
                                <a href="/business/{{$business->id}}"><strong>{{ $business->name }}</strong></a>
                            </td>
                            <td>
                                <a href="/user/{{$business->owner->id}}">{{$business->owner->name}}</a>
                            </td>
                            <td>
                                {{$business->license ? $business->license->advisor->name : 'No advisor.'}}
                            </td>
                            <td>
                                {{$business->accounts()->count()}}
                            </td>
                            <td>
                                <a class="btn btn-info btn-sm" href="/allocations/{{$business->id}}">See Allocations</a>
                            </td>
                            <td>
                                <a class="btn btn-info btn-sm" href="/allocations/{{$business->id}}\percentages">See Percentages</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td scope="row">N/A</td>
                            <td>N/A</td>
                            <td>N/A</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
