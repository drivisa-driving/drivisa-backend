<?php



namespace Modules\Drivisa\Transformers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Purchase;
use Modules\Drivisa\Entities\Transaction;
use Modules\User\Entities\Sentinel\User;
use Modules\User\Transformers\UserTransformer;

class TraineeAdminExportTransformer extends JsonResource
{
    public function toArray($request)
    {
        $instructors = Instructor::whereIn('id',$this->lessons->pluck('instructor_id'))->get();
        $instructorsList ='';
        foreach ($instructors as $instructor){
            $instructorsList .=$instructor->first_name.' '.$instructor->last_name.' , ';
        }
        $purchaseTypes='';
        foreach ($this->purchases  as $purchase)
        {
            $purchaseTypes .=ucwords(str_replace('_', ' ', array_search($purchase->type, Purchase::TYPE))).' ,';
        }
        return [
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'email' => $this->resource->user->email,
            'phone_number' => $this->resource->user->phone_number,
            'postal_code' => $this->resource->user->postal_code,
            'city' => $this->resource->user->city,
            'province' => $this->resource->user->province,
            'birthDate' => $this->resource->birth_date,
            'birthDateYear' => $this->resource->birth_date?Carbon::parse($this->resource->birth_date)->format("Y"):'',
            'age'=>$this->resource->birth_date?Carbon::parse($this->resource->birth_date)->age:'',
            'value_of_sale'=>Transaction::whereIn('id',$this->purchases->pluck('transaction_id'))->sum('amount'),
            'purchase_packages'=>$purchaseTypes,
            'join_date'=>$this->resource->created_at?Carbon::parse($this->resource->created_at)->format("M d Y h:i A"):'',
            'purchase_date'=>$this->purchases->sortByDesc('id')->first()?->created_at?Carbon::parse($this->purchases->sortByDesc('id')->first()?->created_at)->format("M d Y h:i A"):'',
            'instructor_used'=>$instructorsList,
            'lesson_date'=>$this->resource->lessons->sortByDesc('id')->first()?->start_at,
            'review'=>$this->resource->lessons->sortByDesc('id')->first()?->trainee_note
        ];
    }
}
//User::whereIn('id',Instructor::whereIn('id',$this->resource->lessons->pluck('instructor_id')))->pluck('first_name')