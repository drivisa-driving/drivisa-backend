<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\TraineeRepository;
use Modules\Drivisa\Transformers\admin\DashboardTransformer;

class DashboardController extends Controller
{
    private InstructorRepository $instructorRepository;
    private TraineeRepository $traineeRepository;

    /**
     * @param InstructorRepository $instructorRepository
     * @param TraineeRepository $traineeRepository
     */
    public function __construct(
        InstructorRepository $instructorRepository,
        TraineeRepository    $traineeRepository
    ) {
        $this->instructorRepository = $instructorRepository;
        $this->traineeRepository = $traineeRepository;
    }

    public function index()
    {
        $data = [];

        $data['stats']['total_instructors'] = $this->instructorRepository->count();
        $data['stats']['total_instructors_in_day'] = $this->instructorRepository
            ->where("created_at", ">", Carbon::now()->subDay())
            ->count();

        $data['stats']['total_trainees'] = $this->traineeRepository->count();
        $data['stats']['total_trainees_in_day'] = $this->traineeRepository
            ->where("created_at", ">", Carbon::now()->subDay())
            ->count();

        $data['stats']['total_verified_instructors'] = $this->instructorRepository->where('verified', 1)->count();
        $data['stats']['total_verified_instructors_in_day'] = $this->instructorRepository
            ->where('verified', 1)
            ->where("created_at", ">", Carbon::now()->subDay())
            ->count();


        $data['stats']['total_verified_trainees'] = $this->traineeRepository->where('verified', 1)->count();
        $data['stats']['total_verified_trainees_in_day'] = $this->traineeRepository
            ->where('verified', 1)
            ->where("created_at", ">", Carbon::now()->subDay())
            ->count();

        // instructor join in 24 hours
        $data['lists'][0]['name'] = "Today Joined Instructors";
        $data['lists'][0]['base_url'] = "/admin/instructors/details/";
        $data['lists'][0]['data'] = $this->instructorRepository
            ->where("created_at", ">", Carbon::now()->subDay())
            // ->all()
            ->take(5);

        // trainee join in 24 hours
        $data['lists'][1]['name'] = "Today Joined Trainees";
        $data['lists'][1]['base_url'] = "/admin/trainees-details/";
        $data['lists'][1]['data'] = $this->traineeRepository
            ->where("created_at", ">", Carbon::now()->subDay())
            // ->all()
            ->take(5);

        // pending verification instructor
        $data['lists'][2]['name'] = "Pending Verification [Instructor]";
        $data['lists'][2]['base_url'] = "/admin/instructors/details/";
        $data['lists'][2]['data'] = $this->instructorRepository
            ->where('verified', 0)
            ->get()->take(5);


        // pending verification trainee
        $data['lists'][3]['name'] = "Pending Verification [Trainee]";
        $data['lists'][3]['base_url'] = "/admin/trainees-details/";
        $data['lists'][3]['data'] = $this->traineeRepository
            ->where('verified', 0)
            ->get()->take(5);


        // Latest Document Uploaded Instructor
        $data['lists'][4]['name'] = "Latest Document Uploaded [Instructor]";
        $data['lists'][4]['base_url'] = "/admin/instructors/details/";
        $data['lists'][4]['data'] = $this->latestDocumentUploaded('instructor');

        // Latest Document Uploaded Trainee
        $data['lists'][5]['name'] = "Latest Document Uploaded [Trainee]";
        $data['lists'][5]['base_url'] = "/admin/trainees-details/";
        $data['lists'][5]['data'] = $this->latestDocumentUploaded('trainee');


        return new DashboardTransformer($data);
    }

    private function latestDocumentUploaded($user_type)
    {
        $images = $this->getImages($user_type);

        return $this->getUserCollection($images, $user_type);
    }

    /**
     * @param \Illuminate\Support\Collection $users
     * @param $image
     * @return bool
     */
    private function isUserNotExists(\Illuminate\Support\Collection $users, $image): bool
    {
        return $users->filter(function ($user) use ($image) {
            return $user && $user->id == $image->imageable_id;
        })->count() === 0;
    }

    /**
     * @param \Illuminate\Support\Collection $images
     * @return \Illuminate\Support\Collection
     */
    private function getUserCollection(\Illuminate\Support\Collection $images, $user_type): \Illuminate\Support\Collection
    {
        $users = collect();

        foreach ($images as $image) {
            if (stristr($image->imageable_type, 'instructor')) {
                if ($this->isUserNotExists($users, $image)) {
                    $instructor = Instructor::find($image->imageable_id);
                    if ($instructor) {
                        $users->push($instructor);
                    }
                } else {
                    continue;
                }
            } else if (stristr($image->imageable_type, 'trainee')) {
                if ($this->isUserNotExists($users, $image)) {
                    $trainee = Trainee::find($image->imageable_id);
                    if ($trainee) {
                        $users->push($trainee);
                    }
                } else {
                    continue;
                }
            }
        }
        return $users->take(5);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function getImages($user_type): \Illuminate\Support\Collection
    {
        $type = 'Modules\Drivisa\Entities\Instructor';

        if ($user_type == 'trainee') {
            $type = 'Modules\Drivisa\Entities\Trainee';
        }

        $images = DB::table('media__imageables')
            ->where('status', 1)
            ->where('imageable_type', $type)
            ->latest()
            ->get();

        return $images;
    }
}
