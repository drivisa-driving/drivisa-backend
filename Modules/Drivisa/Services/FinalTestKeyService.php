<?php

namespace Modules\Drivisa\Services;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class FinalTestKeyService
{
    public function finalTestKeys($data)
    {
        $queryFinalTestKeys = DB::select("SELECT 
        ftk.id, ftk.title, ftk.parent_id, ftk_parent.title AS parent_title
        FROM drivisa__final_test_keys ftk
        LEFT JOIN drivisa__final_test_keys ftk_parent
        ON ftk_parent.id = ftk.parent_id
        WHERE ftk.parent_id IS NOT NULL");


        $data = [];

        foreach ($queryFinalTestKeys as $key => $finalKey) {
            $data[$finalKey->parent_id]['title'] = $finalKey->parent_title;
            $data[$finalKey->parent_id]['subtitles'][] = [
                'title' => $finalKey->title,
                'id' => $finalKey->id
            ];
        }

        return collect($data)->values()->all();
    }
}
