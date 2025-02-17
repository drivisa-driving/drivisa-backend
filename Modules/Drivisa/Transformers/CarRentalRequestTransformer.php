<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Point;
use Modules\Setting\Facades\Settings;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\WorkingHour;
use Modules\Drivisa\Entities\RentalRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Drivisa\Transformers\admin\PackageTransformer;

class CarRentalRequestTransformer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $instructors = $this->getInstructor();

        $bookingDateTime = Carbon::parse($this->booking_date);

        $pickup_point = $this->pickup_point ? json_decode($this->pickup_point) : null;
        $dropoff_point = $this->dropoff_point ? json_decode($this->dropoff_point) : null;

        $start_time = Carbon::parse($this->booking_time)->subMinutes(90)->format('h:i a');
        $end_time = Carbon::parse($this->booking_time)->addMinutes(60)->format('h:i a');
        $booking_datetime = Carbon::parse($this->booking_date)->format('Y-m-d') . " " . Carbon::parse($this->booking_time)->format('H:i:s');

        return [
            'id' => $this->id,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'booking_time' => $this->booking_time->format('h:i a'),
            'booking_date' => $this->booking_date->format('d-m-Y'),
            'booking_time_formatted' => $this->booking_time->format('h:i a'),
            'booking_date_formatted' => $bookingDateTime->format('D, F d, Y'),
            'time_duration' => $start_time . " to " . $end_time,
            'booking_datetime' => $booking_datetime,
            'status' => array_search($this->status, RentalRequest::STATUS),
            'charge' => $this->getCharge(),
            'request_to_instructor' => count($instructors),
            'package' => new PackageTransformer($this->package),
            'accepted_by' => $this->when($this->status === RentalRequest::STATUS['accepted'], $this->acceptedBy),
            'instructor' => $instructors?->first(),
            'point' => new PointTransformer($this->getPoint($instructors?->first())),
            'pickupPoint' => $pickup_point,
            'dropoffPoint' => $dropoff_point,
            'additional_tax' => $this->additional_tax,
            'additional_cost' => $this->additional_cost,
            'total_distance' => $this->total_distance,
            'additional_km' => $this->additional_km,
            'created_at' => $this->created_at->format('d-m-Y H:i a'),
            'expire_payment_time' => Carbon::parse($this->expire_payment_time)->format('Y-m-d H:i:s'),
            'is_reschedule_request' => $this->is_reschedule_request
        ];
    }

    private function getInstructor()
    {
        $instructor_ids = DB::table('drivisa__instructor_rental_request')
            ->where('rental_request_id', $this->id)
            ->get()
            ->pluck('instructor_id')->toArray();

        return Instructor::whereIn('id', $instructor_ids)->get();
    }

    private function getCharge()
    {

        $base_price = $this->package->packageData->discount_price;
        $tax = Settings::get('lesson_tax');

        return $base_price + ($base_price * ($tax / 100));
    }

    private function getPoint($instructor)
    {
        $lesson = $this->trainee?->lessons()->whereNotNull('ended_at')->where('instructor_id', $instructor->id)->latest('ended_at')->first();

        if ($lesson) {
            $workingHour = WorkingHour::find($lesson->working_hour_id);
            if ($workingHour) {
                return Point::find($workingHour->point_id);
            }
        }

        return $instructor->points()->where('is_active', true)->first();
    }
}
