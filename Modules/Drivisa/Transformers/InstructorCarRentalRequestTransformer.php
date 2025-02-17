<?php

namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\RentalRequest;
use Modules\Setting\Facades\Settings;

class InstructorCarRentalRequestTransformer extends JsonResource
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
        if($this->pickup_point) {
            $pickup_point = [
                'address' => $pickup_point->address,
                'latitude' => (float)$pickup_point->latitude,
                'longitude' => (float)$pickup_point->longitude,
            ];
        }
        if($this->dropoff_point) {
            $dropoff_point = [
                'address' => $dropoff_point->address,
                'latitude' => (float)$dropoff_point->latitude,
                'longitude' => (float)$dropoff_point->longitude,
            ];
        }
        $start_time = Carbon::parse($this->booking_time)->subMinutes(90)->format('h:i a');
        $end_time = Carbon::parse($this->booking_time)->addMinutes(60)->format('h:i a');
        $booking_datetime = Carbon::parse($this->booking_date)->format('Y-m-d') . " " . Carbon::parse($this->booking_time)->format('H:i:s');

        return [
            'id' => $this->id,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'booking_time' => $this->booking_time,
            'booking_date' => $this->booking_date,
            'booking_time_formatted' => $this->booking_time->format('h:i a'),
            'booking_date_formatted' => $bookingDateTime->format('D, F d, Y'),
            'time_duration' => $start_time . " to " . $end_time,
            'booking_datetime' => $booking_datetime,
            'pickupPoint' => $pickup_point,
            'dropoffPoint' => $dropoff_point,
            'status' => array_search($this->status, RentalRequest::STATUS),
            'charge' => $this->getCharge(),
            'purchase_id' => $this->purchase_id,
            'request_to_instructor' => count($instructors),
            'package' => $this->package->load('packageData'),
            'accepted_by' => $this->when($this->status === RentalRequest::STATUS['accepted'], $this->acceptedBy),
            'created_at' => $this->created_at->format('d-m-Y H:i a'),
            'trainee' => new TraineeTransformer($this->trainee),
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

        $base_price = $this->package->packageData?->discount_price;
        $tax = Settings::get('lesson_tax');

        return $base_price + ($base_price * ($tax / 100));
    }
}
