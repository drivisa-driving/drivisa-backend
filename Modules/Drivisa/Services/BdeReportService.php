<?php

namespace Modules\Drivisa\Services;

use Modules\Drivisa\Entities\Trainee;
use Modules\Drivisa\Entities\MarkingKeyLog;
use Modules\Drivisa\Entities\MarkingKey;
use Modules\Drivisa\Entities\Lesson;
use Modules\Drivisa\Entities\FinalTestResult;
use Modules\Drivisa\Entities\FinalTestLog;
use Modules\Drivisa\Entities\BDELog;
use Carbon\Carbon;

class BdeReportService
{
    public Trainee $trainee;

    public function getReport(Trainee $trainee)
    {
        $this->trainee = $trainee->load(['user', 'bdeLog.instructor', 'bdeLog.markingKeyLog.markingKey']);
        return [
            'trainee' => $this->getTraineeInfo(),
            'instructors' => $this->getInstructorsList(),
            'bde_log_list' => $this->getBdeLogList(),
            'marking_keys' => $this->getMarkingKeys(),
            'marking_log_list' => $this->getMarkingLogList(),
            'final_test_log' => $this->getFinalTestLog(),
            'final_test_result' => $this->getFinalTestResult()
        ];
    }

    private function getTraineeInfo()
    {
        return [
            'name' => $this->trainee->first_name . " " . $this->trainee->last_name,
            'address' => $this->trainee->user->address,
            'city' => $this->trainee->user->city,
            'postal_code' => $this->trainee->user->postal_code,
            'phone_number' => $this->trainee->user->phone_number,
        ];
    }

    private function getInstructorsList()
    {
        $bdeLogs = $this->trainee->bdeLog;
        $instructors = [];
        foreach ($bdeLogs as $bdeLog) {
            $instructors[$bdeLog?->instructor->id] = [
                'full_name' => $bdeLog?->instructor?->full_name,
                'di_number' => $bdeLog?->instructor?->di_number
            ];
        }

        return array_values($instructors);
    }

    private function getBdeLogList()
    {
        $bdeLessons = $this->getTraineeBdeLessons($this->trainee);
        $list = [];

        foreach ($bdeLessons as $bdeLesson) {

            $bdeLogs = $this->getBdeLogs($bdeLesson);

            foreach ($bdeLogs as $bdeLog) {
                $list[] = [
                    'date' => Carbon::parse($bdeLog->lesson->start_at)->format('m/d/Y'),
                    'time_in' => Carbon::parse($bdeLog->lesson->started_at)->format('h:i a'),
                    'time_out' => Carbon::parse($bdeLog->lesson->ended_at)->format('h:i a'),
                    'instructor_sign' => 'data:image/png;base64,' . $bdeLog->instructor_sign,
                    'trainee_sign' => 'data:image/png;base64,' . $bdeLog->trainee_sign,
                    'notes' => $bdeLog->notes,
                ];
            }
        }

        return $list;
    }

    private function getMarkingKeys()
    {
        return (array)MarkingKey::get(['title'])->pluck('title')->toArray();
    }

    private function getMarkingLogList()
    {
        $bdeLessons = $this->getTraineeBdeLessons($this->trainee);
        $list = [];

        foreach ($bdeLessons as $bdeLesson) {

            $bdeLogs = $this->getBdeLogs($bdeLesson);

            foreach ($bdeLogs as $bdeLog) {

                $markingKeyLogs = MarkingKeyLog::where('bde_log_id', $bdeLog->id)->first();

                if ($markingKeyLogs) {
                    foreach ($bdeLog->markingKeyLog as $markingKeyLog) {
                        $list[$bdeLog->id][$markingKeyLog->markingKey->title] = [
                            'value' => array_search($markingKeyLog->mark, MarkingKeyLog::MARK)
                        ];
                    }
                } else {
                    $list[$bdeLog->id]['title'] = [
                        'value' => null
                    ];
                }
            }
        }
        return array_values($list);
    }

    private function getFinalTestLogBackup()
    {
        $bdeLogs = $this->trainee->bdeLog;

        $finalTestResult = FinalTestResult::whereIn('bde_log_id', $bdeLogs->pluck('id')->toArray())->get();

        $finalTestLogs = FinalTestLog::with(['finalTestKey'])->where('final_test_result_id', $finalTestResult?->id)->get();

        $finalTestKeyService = new FinalTestKeyService();

        $finalTestKeys = $finalTestKeyService->finalTestKeys([]);

        foreach ($finalTestKeys as $master_key => $finalTestKey) {
            foreach ($finalTestKey['subtitles'] as $key => $subtitle) {
                foreach ($finalTestLogs as $finalTestLog) {
                    if ($finalTestLog->final_test_key_id === $subtitle['id']) {
                        $finalTestKeys[$master_key]['subtitles'][$key]['mark_first'] = $finalTestLog->mark_first;
                        $finalTestKeys[$master_key]['subtitles'][$key]['mark_second'] = $finalTestLog->mark_second;
                        $finalTestKeys[$master_key]['subtitles'][$key]['mark_third'] = $finalTestLog->mark_third;
                    }
                }
            }
        }
        return $finalTestKeys;
    }

    private function getFinalTestResultBackup()
    {
        $bdeLogs = $this->trainee->bdeLog;

        $finalTestLog = FinalTestResult::whereIn('bde_log_id', $bdeLogs->pluck('id')->toArray())->first();

        return [
            'bde_log_id' => $finalTestLog?->bde_log_id,
            'final_marks' => $finalTestLog?->final_marks,
            'is_pass' => $finalTestLog?->is_pass,
            'instructor_name' => $finalTestLog?->instructor->full_name,
            'instructor_sign' => 'data:image/png;base64,' . $finalTestLog?->instructor_sign,
            'di_number' => $finalTestLog?->di_number,
            'test_date' => $finalTestLog ? Carbon::parse($finalTestLog->created_at)->format('m/d/Y') : null,
        ];
    }

    private function getTraineeBdeLessons($trainee)
    {
        return $trainee->lessons->where('lesson_type', Lesson::TYPE['bde'])
            ->whereNotNull('ended_at');
    }

    private function getBdeLogs($bdeLesson)
    {
        $diffInHours = $bdeLesson->getDurationAttribute();
        return BDELog::where('lesson_id', $bdeLesson->id)->latest('created_at')->take($diffInHours)->get();
    }

    private function getFinalTestLog()
    {
        $bdeLogs = $this->trainee->bdeLog;

        $finalTestResults = FinalTestResult::whereIn('bde_log_id', $bdeLogs->pluck('id')->toArray())->get();

        $finalTestKeys = [];

        foreach ($finalTestResults as $finalTestResult) {
            $finalTestLogs = FinalTestLog::with(['finalTestKey'])
                ->where('final_test_result_id', $finalTestResult->id)
                ->get();

            $finalTestKeyService = new FinalTestKeyService();
            $finalTestKeysForResult = $finalTestKeyService->finalTestKeys([]);

            foreach ($finalTestKeysForResult as $master_key => $finalTestKey) {
                foreach ($finalTestKey['subtitles'] as $key => $subtitle) {
                    foreach ($finalTestLogs as $finalTestLog) {
                        if ($finalTestLog->final_test_key_id === $subtitle['id']) {
                            $finalTestKeysForResult[$master_key]['subtitles'][$key]['mark_first'] = $finalTestLog->mark_first;
                            $finalTestKeysForResult[$master_key]['subtitles'][$key]['mark_second'] = $finalTestLog->mark_second;
                            $finalTestKeysForResult[$master_key]['subtitles'][$key]['mark_third'] = $finalTestLog->mark_third;
                        }
                    }
                }
            }

            $finalTestKeys[] = $finalTestKeysForResult;
        }

        return $finalTestKeys;
    }

    private function getFinalTestResult()
    {
        $bdeLogs = $this->trainee->bdeLog;

        $finalTestResults = FinalTestResult::whereIn('bde_log_id', $bdeLogs->pluck('id')->toArray())->get();

        $finalTestResultsData = [];

        foreach ($finalTestResults as $finalTestResult) {
            $finalTestResultsData[] = [
                'bde_log_id' => $finalTestResult->bde_log_id,
                'final_marks' => $finalTestResult->final_marks,
                'is_pass' => $finalTestResult->is_pass,
                'instructor_name' => $finalTestResult->instructor->full_name,
                'instructor_sign' => 'data:image/png;base64,' . $finalTestResult->instructor_sign,
                'di_number' => $finalTestResult->di_number,
                'test_date' => Carbon::parse($finalTestResult->created_at)->format('m/d/Y'),
            ];
        }

        return $finalTestResultsData;
    }
}
