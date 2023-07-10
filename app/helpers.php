<?php

use App\Models\Business;
use App\Models\License;
use Illuminate\Contracts\Support\DeferringDisplayableValue;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Env;
use Illuminate\Support\HigherOrderTapProxy;
use Illuminate\Support\Optional;
use JamesMills\LaravelTimezone\Timezone;
use Carbon\Carbon;

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

if(!function_exists('checkLicenseStatus')) {
    function checkLicenseStatus($licenseId)
    {
        $checkStatus = License::find($licenseId);
        
        if($checkStatus->active == 1){
            $today = new Timezone();
            $today = $today->convertToLocal(Carbon::now(), 'Y-m-d H:i:s');
            if (
                Carbon::parse($checkStatus->expires_ts)->timestamp - Carbon::parse($today)->timestamp > 0
                && $checkStatus->active
            ) {
                return true;
            }

            return false;
        } else {
            return false;
        }

        return $status;
    }
}

if(!function_exists('checkNegativeLicense')) {
    function checkNegativeLicense($seats_count, $license_id) 
    {
        if(!$seats_count) {
            $checkSeatNegPos = false;
        } else {
            $checkSeatNegPos = true;
        }
        
        $licenseActiveInactive = checkLicenseStatus($license_id);

        $response = [
            'seats_count' => $checkSeatNegPos,
            'licenseActiveInactive' => $licenseActiveInactive,
        ];

        return $response;
    }
}

if(!function_exists('getAvailable_seats')){
    function getAvailable_seats($currentUser,$businesses){
        $currentUser = Auth::user();
        $seat_count = 0;
        foreach($businesses as $bus){
            if($bus->license->advisor_id == $currentUser->id && $currentUser->isAdvisor()){
                if ( is_object($bus->license) ){
                    if(checkLicenseStatus($bus->license->id) == true){
                        $seat_count++;
                    }
                }
            }
        }
        
        return $available_seats = $currentUser->seats - $seat_count;
    }
}
