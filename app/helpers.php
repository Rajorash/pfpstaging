<?php

use App\Models\Business;
use App\Models\License;
use Illuminate\Contracts\Support\DeferringDisplayableValue;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Env;
use Illuminate\Support\HigherOrderTapProxy;
use Illuminate\Support\Optional;

if (!function_exists('checkActiveInactive')) {
    function checkActiveInactive($user)
    {
        $advisor_id = [];
        $business_id = [];
        $singleArray = [];
        $singleArray2 = [];

        if ($user->id) {
            $getBusinessID = Business::where('owner_id', $user->id)->pluck('id');

            if (count($getBusinessID) > 0) {
                $getStatus = License::whereIn('business_id', $getBusinessID)->pluck('active');

                $array = json_decode(json_encode($getStatus), true);
                $exists = in_array('1', $array);

                if ($exists) {
                    return true;
                } else {
                    return false;
                }
            } else {
                foreach ($user->activeLicenses as $test) {
                    $advisor_id[] = [
                        'id' => $test->pivot->advisor_id,
                    ];

                    $business_id[] = [
                        'id' => $test->pivot->business_id,
                    ];
                }

                $mixArray = [
                    'ad_id' => $advisor_id,
                    'b_id' => $business_id,
                ];

                foreach ($mixArray['ad_id'] as $nestedArray) {
                    $singleArray[] = $nestedArray['id'];
                }

                foreach ($mixArray['b_id'] as $nestedArray2) {
                    $singleArray2[] = $nestedArray2['id'];
                }

                $getStatus = License::whereIn('advisor_id', $singleArray)->whereIn('business_id', $singleArray2)->pluck('active');

                $array2 = json_decode(json_encode($getStatus), true);
                $exists2 = in_array('1', $array2);

                if ($exists2) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
}
