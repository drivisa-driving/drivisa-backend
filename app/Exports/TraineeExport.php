<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Transformers\TraineeAdminExportTransformer;
use Modules\Drivisa\Transformers\TraineeAdminTableTransformer;
use Maatwebsite\Excel\Concerns\WithHeadings;
class TraineeExport implements WithHeadings,FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $request;

    private $traineeRepository ;
     public function __construct($request,$traineeRepository)
     {
         $this->request = $request;
         $this->traineeRepository = $traineeRepository;
     }

    public function collection()
    {
    $search = $this->request->search;

        if ($this->request->type !=='null') {
            if($this->request->search !='null' && $this->request->search !=''){
                return TraineeAdminExportTransformer::collection(
                    Trainee::with('user')->where('first_name','LIKE','%'.$this->request->search.'%')
                        ->orWhere('last_name','LIKE','%'.$search.'%')
                        ->orWhereHas('user',function ($user) use ($search){
                            $user->where('email','LIKE','%'.$search.'%')->orWhere('phone_number','LIKE','%'.$search.'%');
                        })->where('verified', $this->request->type)->get());

            }else{
                return TraineeAdminExportTransformer::collection($this->traineeRepository->where('verified', $this->request->type)->get());
            }

        }else{
            if($this->request->search !='null' && $this->request->search !=''){
                return TraineeAdminExportTransformer    ::collection(
                    Trainee::with('user')->where('first_name','LIKE','%'.$this->request->search.'%')
                        ->orWhere('last_name','LIKE','%'.$this->request->search.'%')
                        ->orWhereHas('user',function ($user) use ($search){
                            $user->where('email','LIKE','%'.$search.'%')->orWhere('phone_number','LIKE','%'.$search.'%');
                        })->get());
            }else{
                return TraineeAdminExportTransformer::collection($this->traineeRepository->all());
             }
        }
    }
    public function headings(): array
    {
        return [
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Zip Code',
            'City',
            'Province',
            'Date of Birth',
            'Date of Birth/Year',
            'Age',
            'Value of Sale',
            'purchase Packages',
            'Join Date',
            'Purchase Date',
            'instructor Used',
            'Lesson Date',
            'Review'
        ];
    }
}