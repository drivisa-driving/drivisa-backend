<?php

namespace Modules\Drivisa\Traits;

use Illuminate\Support\Facades\DB;

trait DocumentTrait
{
    private function updateAllDocumentStatus($data, $entity = "instructor")
    {
        $entity_type = $this->getEntityType($entity);

        DB::table('media__imageables')
            ->where('imageable_type', $entity_type)
            ->where('imageable_id', $data['entity_id'])
            ->update([
                'status' => $data['status'] == 'approved' ? 2 : 3,
                'reason' => $data['reason']
            ]);
    }

    /**
     * @param $entity
     * @return string
     */
    private function getEntityType($entity): string
    {
        if ($entity == "instructor") {
            $entity_type = 'Modules\Drivisa\Entities\Instructor';
        } else if ($entity == "trainee") {
            $entity_type = 'Modules\Drivisa\Entities\Trainee';
        }
        return $entity_type;
    }
}