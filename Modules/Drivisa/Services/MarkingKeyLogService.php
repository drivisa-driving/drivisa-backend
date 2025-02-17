<?php

namespace Modules\Drivisa\Services;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Drivisa\Repositories\MarkingKeyLogRepository;
use Modules\Drivisa\Entities\MarkingKeyLog;

class MarkingKeyLogService
{
    protected $markingKeyLogRepository;
    public function __construct(MarkingKeyLogRepository $markingKeyLogRepository)
    {
        $this->markingKeyLogRepository = $markingKeyLogRepository;
    }

    public function addMarkingKeyLog($data)
    {
        foreach ($data['markings'] as $marking) {
            $this->markingKeyLogRepository->create([
                'bde_log_id' => $data['bde_log_id'],
                'marking_key_id' => $marking['marking_key_id'],
                'mark' => $marking['mark']
            ]);
        }
    }
}
