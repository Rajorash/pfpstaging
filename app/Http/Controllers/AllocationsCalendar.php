<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class AllocationsCalendar extends Controller
{

    protected $currentRangeValue = 14;

    public function calendar(Request $request)
    {
        $business = Business::find($request->business)->with(['accounts'])->first();

        $data = [
            'rangeArray' => $this->getRangeArray(),
            'currentRangeValue' => $this->currentRangeValue,
            'business' => $business
        ];

        return view('v2.allocations-calculator', $data);
    }

    private function getRangeArray()
    {
        return [
            7 => 'Weekly',
            14 => 'Fortnightly',
            31 => 'Monthly'
        ];
    }

    public function updateData(Request $request)
    {

        dd($request);
    }
}
