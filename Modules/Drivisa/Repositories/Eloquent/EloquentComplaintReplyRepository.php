<?php

namespace Modules\Drivisa\Repositories\Eloquent;

use Illuminate\Http\Request;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Drivisa\Repositories\ComplaintReplyRepository;

class EloquentComplaintReplyRepository extends EloquentBaseRepository implements ComplaintReplyRepository
{
    /**
     * @inheritDoc
     */
    public function serverPaginationFilteringFor(Request $request)
    {
        $complaintReply = $this->allWithBuilder();
        return $complaintReply->paginate($request->get('per_page', 50));
    }
}