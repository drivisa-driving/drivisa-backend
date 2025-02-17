<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\WorkingHour;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Drivisa\Entities\RentalRequest;

class MakeRentalRequestAvailableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const EXPIRES_TIME_IN_HOURS_FOR_REQUEST = 24;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // registered rental requests
        $this->registeredRentalRequests();

        // accepted rental requests
        $this->acceptedRentalRequests();
    }

    private function registeredRentalRequests()
    {
        $rentalRequests = RentalRequest::where('status', RentalRequest::STATUS['registered'])
            ->get();

        foreach ($rentalRequests as $rentalRequest) {

            $requestExpireTime = Carbon::parse($rentalRequest->created_at)->addHours(self::EXPIRES_TIME_IN_HOURS_FOR_REQUEST);

            if (now()->greaterThan($requestExpireTime)) {
                $this->makeScheduleAvailable($rentalRequest);
            }
        }
    }

    private function acceptedRentalRequests()
    {
        $rentalRequests = RentalRequest::whereDate('expire_payment_time', today())
            ->where('status', RentalRequest::STATUS['accepted'])
            ->get();

        foreach ($rentalRequests as $rentalRequest) {
            if (now()->greaterThan($rentalRequest->expire_payment_time)) {
                $this->makeScheduleAvailable($rentalRequest);
            }
        }
    }

    private function makeScheduleAvailable($rentalRequest)
    {
        $instructor = Instructor::where('id', $rentalRequest->instructor_id)->first();
        $workingDays = $instructor->workingDays()->get();

        foreach ($workingDays as $workingDay) {
            foreach ($workingDay->workingHours as $workingHour) {

                $start_at = Carbon::parse($rentalRequest->booking_time)->subMinutes(90);
                $date = Carbon::parse($workingDay->date);
                $open_at = Carbon::parse($workingHour->open_at);

                if (
                    $workingHour
                    && $workingHour->status === WorkingHour::STATUS['unavailable']
                    && $rentalRequest->booking_date->equalTo($date)
                    && $start_at->equalTo($open_at)
                ) {
                    $workingHour->status = WorkingHour::STATUS['available'];
                    $workingHour->save();
                }
            }
        }
    }
}
