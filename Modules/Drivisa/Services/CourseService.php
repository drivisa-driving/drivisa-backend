<?php

namespace Modules\Drivisa\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Modules\Drivisa\Entities\Course;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Repositories\CourseRepository;
use Modules\Drivisa\Repositories\EvaluationIndicatorRepository;
use Modules\Drivisa\Repositories\LessonRepository;
use Stripe\PaymentIntent;
use Stripe;

class CourseService
{
    private $lessonRepository;
    private $courseRepository;
    private $stripeService;

    public function __construct(
        LessonRepository $lessonRepository,
        CourseRepository $courseRepository,
        StripeService    $stripeService
    ) {
        $this->lessonRepository = $lessonRepository;
        $this->courseRepository = $courseRepository;
        $this->stripeService = $stripeService;
    }

    public function addCourse($data)
    {
        return $this->courseRepository->create($data);
    }

    public function subscription($trainee, $course, $data)
    {
        $paymentIntent = $this->SavePaymentIntent($data['payment_method_id'], $course->cost);
        $balance_transaction = $paymentIntent->charges->data[0]->balance_transaction;
        $trainee->courses()->attach(
            $course->id,
            ['transaction_id' => $balance_transaction, 'available_hours' => $course->count_hour]
        );
    }

    private function SavePaymentIntent($payment_method_id, $totalCost)
    {
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $paymentIntent = PaymentIntent::create([
            'payment_method' => $payment_method_id,
            'amount' => $totalCost * 100,
            'currency' => 'cad',
            'confirmation_method' => 'manual',
            'confirm' => true,
        ]);

        return $paymentIntent;
    }

    public function cancelCourse($course, $data)
    {
        $course->status = Course::STATUS['canceled'];
        $course->save();

        $this->stripeService->refund($course->payment_intent_id, 50);
    }

    public function refundCredit($data)
    {
        return $this->courseRepository->create([
            'user_id' => $data['user_id'],
            'status' => Course::STATUS['initiated'],
            'credit' => $data['duration'],
            'type' => Course::TYPE['Refund'],
            'previous_course_id' => $data['previous_course_id']
        ]);
    }

    public function refundCancelledCredit($data)
    {
        return $this->courseRepository->create([
            'user_id' => $data['user_id'],
            'status' => Course::STATUS['initiated'],
            'credit' => $data['duration'],
            'type' => Course::TYPE['Cancelled_Credits'],
            'previous_course_id' => $data['previous_course_id']
        ]);
    }

    public function refundBdeCredit($data)
    {
        return $this->courseRepository->create([
            'user_id' => $data['user_id'],
            'status' => Course::STATUS['initiated'],
            'credit' => $data['duration'],
            'type' => Course::TYPE['Refund_BDE'],
            'previous_course_id' => $data['previous_course_id']
        ]);
    }

    public function refundBonusBdeCredit($data)
    {
        return $this->courseRepository->create([
            'user_id' => $data['user_id'],
            'status' => Course::STATUS['initiated'],
            'credit' => $data['duration'],
            'type' => Course::TYPE['Bonus_BDE'],
            'previous_course_id' => $data['previous_course_id']
        ]);
    }

    public function refundBonusCredit($data)
    {
        return $this->courseRepository->create([
            'user_id' => $data['user_id'],
            'status' => Course::STATUS['initiated'],
            'credit' => $data['duration'],
            'type' => Course::TYPE['Bonus'],
            'previous_course_id' => $data['previous_course_id']
        ]);
    }
}
