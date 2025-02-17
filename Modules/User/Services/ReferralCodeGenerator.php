<?php

namespace Modules\User\Services;

use Modules\User\Entities\ReferralCode;
use Modules\User\Repositories\ReferralCodeRepository;

class ReferralCodeGenerator
{
    /**
     * @throws \Exception
     */
    public function generate()
    {
        $code = random_int(100000, 999999);
        while ($this->checkCodeExistsInDatabase($code)) {
            $code = random_int(100000, 999999);
        }
        return $code;
    }

    public function checkCodeExistsInDatabase($code)
    {
        return ReferralCode::where('code', $code)->whereNull('sent_at')->exists();
    }
}