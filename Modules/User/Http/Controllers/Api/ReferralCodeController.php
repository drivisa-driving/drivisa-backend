<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\User\Repositories\ReferralCodeRepository;
use Modules\User\Services\ReferralCodeGenerator;

class ReferralCodeController extends ApiBaseController
{
    public function __construct(public ReferralCodeRepository $referralCodeRepository)
    {

    }

    /**
     * @throws \Exception
     */
    public function codeSent(Request $request)
    {
        try {
            if ($request->code) {
                $this->referralCodeRepository
                    ->where('code', $request->code)
                    ->whereNull('sent_at')
                    ->whereNull('used_at')
                    ->update([
                        'sent_at' => now()
                    ]);
            }

            return response()->json(['message' => 'code update and new code generated', 'new_code' => $this->generateNewCode()]);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return int
     * @throws \Exception
     */
    private function generateNewCode(): int
    {
        $referralCodeGenerator = new ReferralCodeGenerator();
        $newCode = $referralCodeGenerator->generate();

        $this->referralCodeRepository->create([
            'user_id' => auth()->user()->id,
            'code' => $newCode
        ]);

        return $newCode;
    }
}
