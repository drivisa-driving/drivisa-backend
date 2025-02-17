<?php

namespace Modules\Drivisa\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Entities\Package;
use Modules\Drivisa\Repositories\CourseRepository;
use Modules\Drivisa\Repositories\PackageRepository;
use Modules\Drivisa\Repositories\PackageTypeRepository;
use Modules\Drivisa\Services\CourseService;
use Modules\User\Repositories\UserRepository;
use Stripe;

class StripeWebhookController extends Controller
{
    const COURSE_MIN_PRICE = 550;
    private UserRepository $userRepository;
    private CourseRepository $courseRepository;
    private CourseService $courseService;
    private PackageTypeRepository $packageTypeRepository;
    private PackageRepository $packageRepository;

    /**
     * @param UserRepository $userRepository
     * @param CourseRepository $courseRepository
     * @param CourseService $courseService
     */
    public function __construct(
        UserRepository        $userRepository,
        CourseRepository      $courseRepository,
        CourseService         $courseService,
        PackageTypeRepository $packageTypeRepository,
        PackageRepository     $packageRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->courseRepository = $courseRepository;
        $this->courseService = $courseService;
        $this->packageTypeRepository = $packageTypeRepository;
        $this->packageRepository = $packageRepository;
    }

    public function handle(Request $request)
    {

        try {

            // if payment failed or other than succeeded
            if ($request['type'] !== 'payment_intent.succeeded') return;

            $data = $request['data'];
            $intent = $data['object'];

            //if payment amount is less than course price
            if ($intent['amount'] <= self::COURSE_MIN_PRICE * 100) return;

            $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));

            $intent = $stripe->paymentIntents->retrieve($intent['id']);

            if (count($intent['charges']['data'] === 0)) return;

            $billing_details = $intent['charges']['data'][0]['billing_details'];

            // find user
            $user = null;

            if (isset($billing_details['email'])) {
                $user = $this->userRepository->findByAttributes(['email' => $billing_details['email']]);
            }

            if (!$user && isset($billing_details['phone'])) {
                $user = $this->userRepository->findByAttributes(['phone_number' => $billing_details['phone']]);
            }

            // if user not found
            if (!$user) return;

            $course = $this->courseRepository->findByAttributes(['payment_intent_id' => $intent['id']]);

            if ($course) return;

            // handle course request here

            $user = $this->userRepository->findByAttributes(['email' => 'trainee@test.com']);

            $package = $this->packageRepository->with('packageData')->where('name', '10 Credit Package')->first();

            if (!$package) return;

            $course = $this->courseService->addCourse([
                'package_id' => $package->id,
                'user_id' => $user->id,
                'type' => Course::TYPE['BDE'],
                'status' => Course::STATUS['initiated'],
                'credit' => $package->packageData->hours,
                'payment_intent_id' => $intent['id']
            ]);

            Log::info($course);

        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
    }
}
