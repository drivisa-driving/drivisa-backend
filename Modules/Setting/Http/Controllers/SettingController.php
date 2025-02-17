<?php

namespace Modules\Setting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\Api\ApiBaseController;
use Modules\Setting\Http\Requests\SettingCreateRequest;
use Modules\Setting\Repositories\SettingRepository;
use Modules\Setting\Services\SettingService;
use Modules\Setting\Transformers\SettingTransformer;

class SettingController extends ApiBaseController
{
    /**
     * @var settingService
     */
    private $settingService;


    /**
     * @param SettingService $settingService
     */
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function allSettings(Request $request)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $settings = $this->settingService->getSettings();
            return SettingTransformer::collection($settings);
        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function store(SettingCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $data = $request->only('name', 'value');

            $setting = $this->settingService->createSetting($data);
            DB::commit();
            $message = trans('setting::settings.message.setting_added', [], $request->get('locale'));
            return $this->successMessage($message);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function getSingleSetting(Request $request, $id)
    {
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $setting = $this->settingService->getSingleSetting($id);
            if ($setting) {
                return new SettingTransformer($setting);
            } else {
                $message = trans("setting::settings.message.no_setting_found", [], $request->get('locale'));
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

        } catch (\Exception $e) {
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }

            $data = $request->only('value');

            $setting = $this->settingService->updateSetting($id, $data);
            DB::commit();
            $message = trans('setting::settings.message.setting_updated', [], $request->get('locale'));
            return $this->successMessage($message);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }

    public function delete(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getUserFromRequest($request);
            if (!$authUser) {
                $message = trans('user::users.messages.user_not_found');
                return $this->errorMessage($message, Response::HTTP_NOT_FOUND);
            }
            $this->settingService->destroy($id);
            DB::commit();
            $message = trans('setting::settings.message.setting_deleted', [], $request->get('locale'));
            return $this->successMessage($message);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorMessage($e->getMessage(), $e->getCode());
        }
    }
}
