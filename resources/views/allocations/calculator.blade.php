@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <h1>{{$business->name}} Allocations</h1>
    </div>
    <div class="row">
        <table class="table table-hover table-sm table-responsive">
            <thead class="thead-inverse">
                <tr>
                    <th></th>
                    <th>Jan 1</th>
                    <th>Jan 2</th>
                    <th>Jan 3</th>
                    <th>Jan 4</th>
                    <th>Jan 5</th>
                    <th>Jan 6</th>
                    <th>Jan 7</th>
                    <th>Jan 8</th>
                    <th>Jan 9</th>
                    <th>Jan 10</th>
                    <th>Jan 11</th>
                    <th>Jan 12</th>
                    <th>Jan 13</th>
                    <th>Jan 14</th>
                    <th>Jan 15</th>
                    <th>Jan 16</th>
                    <th>Jan 17</th>
                    <th>Jan 18</th>
                    <th>Jan 19</th>
                    <th>Jan 20</th>
                    <th>Jan 21</th>
                    <th>Jan 22</th>
                    <th>Jan 23</th>
                    <th>Jan 24</th>
                    <th>Jan 25</th>
                    <th>Jan 26</th>
                    <th>Jan 27</th>
                    <th>Jan 28</th>
                    <th>Jan 29</th>
                    <th>Jan 30</th>
                    <th>Jan 31</th>
                </tr>
                </thead>
                <tbody>
                    @forelse ($business->accounts as $acc)
                    <tr style="border-top: 2px solid #99ccdd;">
                        <td scope="row" style="background-color:#99ccdd;">{{$acc->name}}</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    @forelse ($acc->flows as $flow)
                    <tr>
                        <td style="background-color: {{$flow->negative_flow ? '#dd9999' : '#99dd99'}}" scope="row">{{$flow->label}}</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    @empty
                    @endforelse
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

@endsection