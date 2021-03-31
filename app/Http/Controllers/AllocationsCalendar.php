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
        $startDate = $request->startDate;
        $rangeValue = $request->rangeValue;

        $response = [
            'error' => [],
            'html' => [],
        ];

        if (!$startDate) {
            $response['error'][] = 'Start date not set';
        }

        if (!$rangeValue) {
            $response['error'][] = 'Range value not set';
        }

        $tableData = [];

        $response['html'] = view('v2.allocation-table')->with('tableData', $tableData)->render();

        return response()->json($response);
    }
}
