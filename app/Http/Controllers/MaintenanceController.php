<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function maintenance(Request $request)
    {
        $code = $request->get('code') ?? null;

        return view('maintenance/maintenance', ['code' => $code]);
    }
}
