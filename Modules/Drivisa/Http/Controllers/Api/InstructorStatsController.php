<?php

namespace Modules\Drivisa\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Repositories\InstructorRepository;

class InstructorStatsController extends ApiBaseController
{
    public function __construct(
        public InstructorRepository $instructor
    )
    {

    }

    public function missingInfo(Request $request)
    {
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $instructor = $this->instructor->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'data' => [
                    'document_submitted' => $this->documentSubmitted($instructor),
                    'bank_account_submitted' => $this->bankAccountSubmitted($instructor),
                    'car_added' => $this->carAdded($instructor),
                    'location_added' => $this->locationAdded($instructor),
                    'document_signed' => $this->documentSigned($instructor),
                    'profile_finished' => $this->profileFinished($instructor),
                    'document_in_review' => $this->documentInReview($instructor),
                ]
            ]);

        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    private function documentSubmitted($instructor): bool
    {
        return $instructor->files->count() >= 9;
    }

    private function bankAccountSubmitted($instructor): bool
    {
        if(!$instructor->stripeAccount) return false;
        
        $stripeClient = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $account = $stripeClient->accounts->retrieve($instructor->stripeAccount?->account_id);
        return $account?->capabilities['card_payments'] === 'active' && $account?->capabilities['transfers'] === 'active';
    }


    private function carAdded($instructor): bool
    {
        return $instructor->cars->count() > 0;
    }

    private function locationAdded($instructor): bool
    {
        return $instructor->points->count() > 0;
    }

    private function documentSigned($instructor): bool
    {
        return !!$instructor->signed_agreement;
    }

    private function profileFinished($instructor): bool
    {
        return $instructor->bio
            && $instructor->user->phone_number
            && $instructor->user->postal_code
            && $instructor->user->city
            && $instructor->di_number
            && $instructor->licence_end_date
            && $instructor->di_end_date
            && $instructor->licence_number
            && $instructor->languages
            && $instructor->signed_at;
    }

    private function documentInReview($instructor): bool
    {
        $files = $instructor->files;
        if($files->count() > 0) {
            foreach ($files as $file) {
                if($file?->pivot?->status != 2) return true;
            }
        }
        return false;
    }
}
