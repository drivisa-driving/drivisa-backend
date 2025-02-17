<?php

namespace Modules\Drivisa\Services;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\Drivisa\Repositories\StripeAccountRepository;
use Modules\Drivisa\Repositories\StripeBankAccountRepository;
use Modules\Setting\Entities\Setting;
use Modules\Setting\Facades\Settings;
use Modules\User\Entities\ReferralTransaction;
use stdClass;
use Stripe;

class StripeService
{
    private $instructorRepository;
    private $stripeAccountRepository;
    private $stripeBankAccountRepository;

    const STATIC_REFERRAL_AMOUNT = 5; #in cad
    const STATIC_TAX_VALUE = 0.13; # conversion of 13/100

    public function __construct(
        InstructorRepository        $instructorRepository,
        StripeAccountRepository     $stripeAccountRepository,
        StripeBankAccountRepository $stripeBankAccountRepository
    ) {
        $this->instructorRepository = $instructorRepository;
        $this->stripeAccountRepository = $stripeAccountRepository;
        $this->stripeBankAccountRepository = $stripeBankAccountRepository;
    }

    public function getStripeAccount($instructor)
    {
        $stripeAccount = $instructor->stripeAccount;
        if (!$stripeAccount) {
            throw new Exception(trans('drivisa::drivisa.messages.not_have_account'), Response::HTTP_NOT_FOUND);
        }
        return $stripeAccount;
    }

    public function connectStripeAccount($instructor, $data)
    {
        if ($instructor->stripeAccount) {
            throw new Exception(trans('drivisa::drivisa.messages.have_account'), Response::HTTP_METHOD_NOT_ALLOWED);
        }

        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        // settings.payouts.schedule.weekly_anchor -> payout for day
        $stripeAccount = Stripe\Account::create(
            [
                'type' => 'custom',
                'country' => 'CA',
                'email' => $instructor->user->email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'settings' => [
                    'payouts' => [
                        'schedule' => [
                            'interval' => 'weekly',
                            'weekly_anchor' => 'tuesday',
                        ]
                    ]
                ]
            ]
        );

        $accountId = $stripeAccount['id'];

        $instructor->stripeAccount()->create([
            'account_id' => $accountId
        ]);

        $accountLink = Stripe\AccountLink::create([
            'account' => $accountId,
            'refresh_url' => config('app.url') . '/instructor/bank-account',
            'return_url' => config('app.url') . '/instructor/bank-account',
            'type' => 'account_onboarding',
        ]);

        return $accountLink['url'];
    }

    public function updateConnectedStripeAccount($instructor, $data)
    {
        $stripeAccount = $instructor->stripeAccount;
        if (!$stripeAccount) {
            throw new Exception(trans('drivisa::drivisa.messages.not_have_account'), Response::HTTP_NOT_FOUND);
        }

        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $accountLink = Stripe\AccountLink::create([
            'account' => $stripeAccount->account_id,
            'refresh_url' => config('app.url') . '/instructor/bank-account',
            'return_url' => config('app.url') . '/instructor/bank-account',
            'type' => 'account_onboarding',
            'collect' => 'eventually_due',
        ]);

        return $accountLink['url'];
    }

    public function deleteConnectedStripeAccount($instructor)
    {
        $stripeAccount = $instructor->stripeAccount;
        if (!$stripeAccount) {
            throw new Exception(trans('drivisa::drivisa.messages.not_have_account'), Response::HTTP_NOT_FOUND);
        }

        $stripeClient = new Stripe\StripeClient(env('STRIPE_SECRET'));

        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $stripeClient->accounts->delete($stripeAccount->account_id);

        $instructor->stripeAccount()->delete();
    }

    public function createStripeAccount($instructor, $data)
    {
        if (!$instructor->verified) {
            throw new Exception(trans('drivisa::drivisa.messages.account_not_verified'), Response::HTTP_FORBIDDEN);
        }
        if ($instructor->stripeAccount) {
            throw new Exception(trans('drivisa::drivisa.messages.have_account'), Response::HTTP_METHOD_NOT_ALLOWED);
        }
        $data['city'] = $data['address']['city'];
        $data['line1'] = $data['address']['line1'];
        $data['postal_code'] = $data['address']['postal_code'];
        $data['state'] = $data['address']['state'];
        $data['bank_account']['routing_number'] = $data['bank_account']['transit_number'] . "-" .
            $data['bank_account']['institution_number'];
        $stripeAccount = $instructor->stripeAccount()->create($data);
        $stripeAccount->stripeBankAccount()->create($data['bank_account']);
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $date = Carbon::parse($data['birth_date']);
        $fullName = $data['first_name'] . ' ' . $data['last_name'];
        $bankAccount = $data['bank_account'];
        $bankAccount['object'] = 'bank_account';
        $individual = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'id_number' => $data['id_number'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
            'dob' => [
                'day' => $date->day,
                'month' => $date->month,
                'year' => $date->year
            ],
        ];
        $payouts_settings = [
            'payouts' =>
            [
                'schedule' =>
                [
                    'interval' => 'weekly',
                    'weekly_anchor' => 'tuesday',
                ]
            ]
        ];
        $stripeAccount = Stripe\Account::create(
            [
                'type' => 'custom',
                'country' => $data['country'],
                'email' => $data['email'],
                'business_type' => 'individual',
                'business_profile' => [
                    'mcc' => 8299,
                    'product_description' => 'instructor account',
                    'name' => $fullName,
                ],
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
                'tos_acceptance' => [
                    'date' => time(),
                    'ip' => $_SERVER['REMOTE_ADDR'], // Assumes you're not using a proxy
                ],
                'individual' => $individual,
                'external_account' => $bankAccount,
                'settings' => $payouts_settings

            ]
        );
        $instructor->stripeAccount()->update(['account_id' => $stripeAccount->id]);
    }

    public function updateStripeAccount($instructor, $data)
    {
        $stripeAccount = $instructor->stripeAccount;
        if (!$stripeAccount) {
            throw new Exception(trans('drivisa::drivisa.messages.not_have_account'), Response::HTTP_NOT_FOUND);
        }
        $data['city'] = $data['address']['city'];
        $data['line1'] = $data['address']['line1'];
        $data['postal_code'] = $data['address']['postal_code'];
        $data['state'] = $data['address']['state'];
        $data['bank_account']['routing_number'] = $data['bank_account']['transit_number'] . $data['bank_account']['institution_number'];
        $stripeAccount->update($data);

        $stripeAccount->stripeBankAccount->update($data['bank_account']);


        $date = Carbon::parse($data['birth_date']);
        $fullName = $data['first_name'] . ' ' . $data['last_name'];
        $bankAccount = $data['bank_account'];
        $bankAccount['object'] = 'bank_account';
        $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
        $individual = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'id_number' => $data['id_number'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
            'dob' => [
                'day' => $date->day,
                'month' => $date->month,
                'year' => $date->year
            ],
        ];

        $stripe->accounts->update(
            $stripeAccount->account_id,
            [
                'email' => $data['email'],
                'business_profile' => [
                    'name' => $fullName,
                ],
                'individual' => $individual,
                'external_account' => $bankAccount
            ]
        );

        return $stripeAccount;
    }

    public function deleteStripeAccount($instructor)
    {
        $stripeAccount = $instructor->stripeAccount;
        if (!$stripeAccount) {
            throw new Exception(trans('drivisa::drivisa.messages.not_have_account'), Response::HTTP_NOT_FOUND);
        }
        $stripeAccount->delete();
        $stripeAccount->stripeBankAccount()->delete();
        $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
        $account = $stripe->accounts->delete($stripeAccount->account_id);
        if (!$account->isDeleted()) {
            throw new Exception(trans('drivisa::drivisa.messages.something_wrong'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getEarnings($instructor)
    {
        $stripeAccount = $instructor->stripeAccount;
        if (!$stripeAccount) {
            return null;
        }
        $account_id = $stripeAccount->account_id;
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $balance = \Stripe\Balance::retrieve(
            ['stripe_account' => $account_id]
        );
        $page = request('starting_after', null);
        $perPage = request('per_page', 50);
        $from = request('from', null);
        $to = request('to', null);
        $transaction = \Stripe\BalanceTransaction::all(
            [
                'starting_after' => $page,
                'limit' => $perPage,
                'type' => 'payment',
                'created' => [
                    'gte' => $from ? strtotime($from . '00:00') : null,
                    'lte' => $to ? strtotime($to . '23:59') : null,
                ],
            ],
            ['stripe_account' => $account_id]
        );
        $data = new stdClass();
        $data->transactions = $transaction->data;
        $data->balance = $balance;

        return $data;
    }

    public function refund(
        $intent_id,
        $cancellationFee = null,
        $instructor_account = null,
        $instructor_refund_amount = 0
    ) {
        $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $intent = $stripe->paymentIntents->retrieve($intent_id);

        $refunds = $stripe->refunds->all(['payment_intent' => $intent_id]);
        $refundedAmount = 0;

        if (!empty($refunds)) {
            foreach ($refunds as $refund) {
                $refundedAmount += $refund->amount;
            }
        }

        $refundableAmount = max($intent->amount - $refundedAmount - ($cancellationFee * 100), 0);

        $object = null;
        if ($refundableAmount > 0) {
            $object = Stripe\Refund::create([
                'payment_intent' => $intent->id,
                'amount' => (int)$refundableAmount,
            ]);
        }

        // transfer to the instructor
        if ($cancellationFee && $instructor_account) {
            Stripe\Transfer::create([
                'amount' => $instructor_refund_amount * 100,
                'currency' => 'cad',
                'destination' => $instructor_account,
                'description' => "Payment transfer to instructor after lesson cancellation"
            ]);
        }

        return $object;
    }

    public function addBankAccount($instructor, $data)
    {
        $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));


        return $stripe->accounts->createExternalAccount(
            $instructor->stripeAccount->account_id,
            ['external_account' => $data['token']]
        );
    }

    public function sendReferralIncome($instructor, $duration = 1): void
    {
        $advocateInstructor = $this->instructorRepository->findByAttributes(['user_id' => $instructor->user->refer_id]);

        if ($advocateInstructor) {
            $stripeAccount = $advocateInstructor->stripeAccount?->account_id;
            if ($stripeAccount) {

                $baseAmount = Settings::get('referral_amount') ?? self::STATIC_REFERRAL_AMOUNT;

                // multiple the total hours to base amount
                $baseAmount = $baseAmount * $duration;

                $amount = $baseAmount + ($baseAmount * self::STATIC_TAX_VALUE);

                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $transferObject = Stripe\Transfer::create([
                    'amount' => $amount * 100,
                    'currency' => 'cad',
                    'destination' => $stripeAccount,
                    "description" => "Referral Amount from " . $instructor->full_name,
                ]);

                ReferralTransaction::create([
                    'receiver_user_id' => $advocateInstructor->user_id,
                    'user_id' => $instructor->user_id,
                    'base_amount' => $baseAmount,
                    'tax' => ($baseAmount * self::STATIC_TAX_VALUE),
                    'amount' => $amount,
                    'data' => isset($transferObject) ? json_encode($transferObject) : json_encode([])
                ]);
            }
        }
    }

    public function refundUpdated(
        $intent_id,
        $cancellationFee = null,
        $instructor_account = null,
        $instructor_refund_amount = 0
    ) {
        $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $intent = $stripe->paymentIntents->retrieve($intent_id);

        $refundAmount = max($intent->amount - ($cancellationFee * 100), 0);

        $object = null;
        if ($refundAmount > 0) {
            $object = Stripe\Refund::create([
                'payment_intent' => $intent->id,
                'amount' => (int)$refundAmount
            ]);
        }

        // transfer to the instructor
        if ($cancellationFee && $instructor_account) {
            Stripe\Transfer::create([
                'amount' => $instructor_refund_amount * 100,
                'currency' => 'cad',
                'destination' => $instructor_account
            ]);
        }

        return $object;
    }
}
