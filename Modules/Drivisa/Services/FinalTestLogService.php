<?php

namespace Modules\Drivisa\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Drivisa\Entities\Instructor;
use Modules\Drivisa\Repositories\FinalTestLogRepository;
use Modules\Drivisa\Repositories\FinalTestResultRepository;
use Modules\Drivisa\Repositories\BDELogRepository;
use Modules\Drivisa\Entities\BDELog;
use Modules\Drivisa\Entities\FinalTestLog;
use Modules\Drivisa\Entities\FinalTestResult;

class FinalTestLogService
{
    const TOTAL_MARKS_IN_FINAL_TEST = 100;
    const MINIMUM_PASSING_MARKS = 80;

    public function __construct(
        public FinalTestLogRepository    $finalTestLogRepository,
        public FinalTestResultRepository $finalTestResultRepository,
        public BDELogRepository          $bdeLogRepository
    ) {
    }

    public function finalTestLog($data)
    {
        $bdeLog = $this->bdeLogRepository->findByAttributes(['id' => $data['bde_log_id']]);

        $instructor = Instructor::find($bdeLog->instructor_id);

        $finalTestResult = $this->finalTestResultRepository->create([
            'bde_log_id' => $bdeLog->id,
            'instructor_id' => $bdeLog->instructor_id,
            'instructor_sign' => $bdeLog->instructor_sign,
            'di_number' => $instructor?->di_number,
            'final_marks' => 0,
            'is_pass' => false
        ]);

        foreach ($data['final_test_keys'] as $finalTestKey) {
            $this->finalTestLogRepository->create([
                'final_test_result_id' => $finalTestResult->id,
                'final_test_key_id' => $finalTestKey['final_test_key_id'],
                'mark_first' => $finalTestKey['mark_first'] ? 1 : 0,
                'mark_second' => $finalTestKey['mark_second'] ? 1 : 0,
                'mark_third' => $finalTestKey['mark_third'] ? 1 : 0
            ]);
        }

        $queryResult = DB::select("SELECT SUM(mark_first + mark_second + mark_third ) total_deduct 
        FROM drivisa__final_test_logs
        WHERE final_test_result_id = " . $finalTestResult->id);

        $total_deduct = count($queryResult) > 0 ? $queryResult[0]->total_deduct : 0;

        $final_marks = self::TOTAL_MARKS_IN_FINAL_TEST - $total_deduct;

        $is_pass = $final_marks >= self::MINIMUM_PASSING_MARKS;

        $finalTestResult->update([
            'final_marks' => $final_marks,
            'is_pass' => $is_pass,
        ]);
    }

    public function addOrUpdateFinalTestLog($trainee, $data)
    {
        $latestBdeLog = BDELog::where('trainee_id', $trainee->id)->latest('updated_at')->first();
        $instructor = Instructor::find($latestBdeLog->instructor_id);

        if ($data['bde_log_id']) {
            $bdeLog = $this->bdeLogRepository->findByAttributes(['id' => $data['bde_log_id']]);
            $finalTestResult = FinalTestResult::where('bde_log_id', $bdeLog->id)->first();

            if ($finalTestResult) {
                $this->updateFinalTestResult($finalTestResult, $latestBdeLog, $instructor);

                $finalTestLogs = FinalTestLog::where('final_test_result_id', $finalTestResult->id)->get();

                foreach ($data['final_test_keys'] as $finalTestKey) {
                    foreach ($finalTestKey['subtitles'] as $subtitle) {

                        $finalTestLog = $finalTestLogs->where('final_test_key_id', $subtitle['final_test_key_id'])->first();

                        if ($finalTestLog) {
                            $this->updateFinalTestLog($finalTestResult, $finalTestLog, $subtitle);
                        } else {
                            $this->createFinalTestLog($finalTestResult, $subtitle);
                        }
                    }
                }
            }
        } else {
            $finalTestResult = $this->createFinalTestResult($latestBdeLog, $instructor);

            foreach ($data['final_test_keys'] as $finalTestKey) {
                foreach ($finalTestKey['subtitles'] as $subtitle) {
                    $this->createFinalTestLog($finalTestResult, $subtitle);
                }
            }
        }

        $this->passOrFail($finalTestResult);
    }

    private function updateFinalTestResult($finalTestResult, $latestBdeLog, $instructor)
    {
        return $finalTestResult->update([
            'bde_log_id' => $latestBdeLog->id,
            'instructor_id' => $latestBdeLog->instructor_id,
            'instructor_sign' => $latestBdeLog->instructor_sign,
            'di_number' => $instructor?->di_number,
        ]);
    }

    private function createFinalTestResult($bdeLog, $instructor)
    {
        return $this->finalTestResultRepository->create([
            'bde_log_id' => $bdeLog->id,
            'instructor_id' => $bdeLog->instructor_id,
            'instructor_sign' => $bdeLog->instructor_sign,
            'di_number' => $instructor?->di_number,
            'final_marks' => 0,
            'is_pass' => false
        ]);
    }

    private function createFinalTestLog($finalTestResult, $finalTestKey)
    {
        if (isset($finalTestKey['mark_first']) || isset($finalTestKey['mark_second']) || isset($finalTestKey['mark_third'])) {
            $finalTestKey['mark_first'] = isset($finalTestKey['mark_first']) ?: false;
            $finalTestKey['mark_second'] = isset($finalTestKey['mark_second']) ?: false;
            $finalTestKey['mark_third'] = isset($finalTestKey['mark_third']) ?: false;

            return $this->finalTestLogRepository->create([
                'final_test_result_id' => $finalTestResult->id,
                'final_test_key_id' => $finalTestKey['final_test_key_id'],
                'mark_first' => $finalTestKey['mark_first'] ? 1 : 0,
                'mark_second' => $finalTestKey['mark_second'] ? 1 : 0,
                'mark_third' => $finalTestKey['mark_third'] ? 1 : 0
            ]);
        }
    }

    private function updateFinalTestLog($finalTestResult, $finalTestLog, $subtitle)
    {
        return $finalTestLog->update([
            'final_test_result_id' => $finalTestResult->id,
            'final_test_key_id' => $subtitle['final_test_key_id'],
            'mark_first' => $subtitle['mark_first'] ? 1 : 0,
            'mark_second' => $subtitle['mark_second'] ? 1 : 0,
            'mark_third' => $subtitle['mark_third'] ? 1 : 0
        ]);
    }

    private function passOrFail($finalTestResult)
    {
        $queryResult = DB::select("SELECT SUM(mark_first + mark_second + mark_third ) total_deduct 
        FROM drivisa__final_test_logs
        WHERE final_test_result_id = " . $finalTestResult->id);

        $total_deduct = count($queryResult) > 0 ? $queryResult[0]->total_deduct : 0;

        $final_marks = self::TOTAL_MARKS_IN_FINAL_TEST - $total_deduct;

        $is_pass = $final_marks >= self::MINIMUM_PASSING_MARKS;

        $finalTestResult->update([
            'final_marks' => $final_marks,
            'is_pass' => $is_pass,
        ]);
    }
}
