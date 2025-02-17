<?php

namespace Modules\Drivisa\Http\Controllers\Api\admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Drivisa\Entities\Instructor;
use Modules\User\Entities\Sentinel\User;
use Modules\Drivisa\Events\InstructorAccountRejected;
use Modules\Drivisa\Events\InstructorAccountVerified;
use Modules\Drivisa\Transformers\DocumentTransformer;
use Modules\Drivisa\Events\InstructorDocumentApproved;
use Modules\Drivisa\Events\InstructorDocumentRejected;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Drivisa\Notifications\SendMessageToUserNotification;

class InstructorDocumentAdminController extends ApiBaseController
{


    public function __construct()
    {
    }

    public function index(Request $request, Instructor $instructor)
    {
        try {
            $files = $instructor->files;
            return DocumentTransformer::collection($files);
        } catch (Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function changeDocumentStatus(Request $request, Instructor $instructor, $document_id)
    {
        DB::beginTransaction();
        try {
            $user = $this->getUserFromRequest($request);
            if (!$user) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            DB::commit();

            $data = [
                'id' => $document_id,
                'status' => $request->status,
                'reason' => $request->message
            ];

            $user = User::find($instructor->user_id);
            $message = $request->message;

            if ($request->status == 'approved') {
                $this->updateStatus($data, $instructor);
                $message = "Your Document Verified";
                // Notify instructor about document verified
                $user->notify(new SendMessageToUserNotification($message));
                event(new InstructorAccountVerified($instructor, "Account Verified"));
                return $this->successMessage("Document Verified");
            } else {
                $this->updateStatus($data, $instructor);
                // Notify instructor about document rejection with reason
                $user->notify(new SendMessageToUserNotification($message));
                event(new InstructorAccountRejected($instructor, $request->message));
                return $this->successMessage("Document Rejected");
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    private function updateStatus($data, $instructor)
    {
        $document = DB::table('media__imageables')
            ->where('file_id', $data['id'])
            ->first();

        DB::table('media__imageables')
            ->where('file_id', $data['id'])
            ->update([
                'status' => $data['status'] == 'approved' ? 2 : 3,
                'reason' => $data['reason']
            ]);

        if ($this->isAllDocumentApproved($document)) {
            $instructor->update([
                'verified' => 1,
                'kyc_verification' => Instructor::KYC['Approved']
            ]);

            event(new InstructorAccountVerified($instructor, "Account Verified"));
        }


        $documentName = ucwords(str_replace("_", " ", $document->zone));


        if ($data['status'] == 'approved') {
            event(new InstructorDocumentApproved($instructor, $documentName));
        } else {
            event(new InstructorDocumentRejected($instructor, $documentName, $data['reason']));
        }
    }

    /**
     * @param $document
     * @return void
     */
    private function isAllDocumentApproved($document): bool
    {
        $documents = DB::table('media__imageables')
            ->where('imageable_id', $document->imageable_id)
            ->where('imageable_type', 'Modules\Drivisa\Entities\Instructor')
            ->get();

        return $documents->filter(function ($document) {
            return $document->status !== 2;
        })->count() === 0;
    }
}
