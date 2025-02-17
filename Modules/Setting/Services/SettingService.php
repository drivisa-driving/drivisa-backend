<?php

namespace Modules\Setting\Services;

use Modules\Setting\Repositories\SettingRepository;

class SettingService
{
    /**
     * @var SettingRepository
     */
    private $settingRepository;

    /**
     * @param SettingRepository $settingRepository
     */
    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function getSettings()
    {
        return $this->settingRepository->all();
    }

    public function getSingleSetting($id)
    {
        return $this->settingRepository->find($id);
    }

    public function createSetting($data)
    {
        return $this->settingRepository->create($data);
    }

    public function updateSetting($id, $data)
    {
        $setting = $this->settingRepository->find($id);
        return $this->settingRepository->update($setting, $data);
    }

    public function destroy($id)
    {
        $setting = $this->settingRepository->find($id);
        return $this->settingRepository->destroy($setting);
    }
}