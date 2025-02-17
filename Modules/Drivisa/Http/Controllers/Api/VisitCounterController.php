<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Entities\VisitCounter;

class VisitCounterController extends ApiBaseController
{
    public function visit($visitType)
    {
       VisitCounter::incrementCounter($visitType);

       $redirectUrl = config('settings.main_url');
       if($visitType == 'page') {
           $redirectUrl = config('settings.download_page_url');
       } else if($visitType == 'ios-app') {
           $redirectUrl = config('settings.ios_app_url');
       } else if($visitType == 'android-app') {
           $redirectUrl = config('settings.android_app_url');
       }

       return redirect($redirectUrl);
    }
}