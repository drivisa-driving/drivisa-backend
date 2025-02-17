<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Entities\BDELog;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\MarkingKeyLog;
use Modules\Drivisa\Repositories\MarkingKeyRepository;

class MarkingKeyService
{

        public function __construct(public MarkingKeyRepository $markingKeyRepository)
        {
                $this->markingKeyRepository = $markingKeyRepository;
        }


        public function getMarkingKeys($trainee_id)
        {
                $lesson = Lesson::where('trainee_id', $trainee_id)->latest('ended_at')->where('lesson_type', 2)->where('status', 3)->first();

                $markingKeys = $this->markingKeyRepository
                        ->allWithBuilder()
                        ->get()
                        ->toArray();

                $new_marking_keys = [];


                if ($lesson) {
                        $bdeLog = BDELog::where('lesson_id', $lesson->id)->latest('updated_at')->first();

                        if ($bdeLog) {
                                $markingKeyLogs = MarkingKeyLog::where('bde_log_id', $bdeLog->id)->get();

                                foreach ($markingKeys as $markingKey) {
                                        $lastValue = $markingKeyLogs->where('marking_key_id', $markingKey['id'])->first();

                                        $markingKey['last_value'] = $lastValue != null ? array_search($lastValue->mark, MarkingKeyLog::MARK) : null;

                                        $new_marking_keys[] = $markingKey;
                                }

                                return $new_marking_keys;
                        } else {
                                foreach ($markingKeys as $markingKey) {
                                        $markingKey['last_value'] = null;

                                        $new_marking_keys[] = $markingKey;
                                }
                                return $new_marking_keys;
                        }
                } else {
                        foreach ($markingKeys as $markingKey) {
                                $markingKey['last_value'] = null;

                                $new_marking_keys[] = $markingKey;
                        }
                        return $new_marking_keys;
                }
        }
}
