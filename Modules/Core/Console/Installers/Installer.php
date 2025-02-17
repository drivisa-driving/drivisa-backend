<?php

namespace Modules\Core\Console\Installers;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Modules\Core\Services\Composer;

class Installer
{
    /**
     * @var array
     */
    protected $scripts = [];

    /**
     * @param Filesystem $finder
     * @param Application $app
     * @param Composer $composer
     */
    public function __construct(
        private Application $app,
        private Filesystem  $finder,
        private Composer    $composer)
    {
    }

    /**
     * @param array $scripts
     * @return $this
     */
    public function stack(array $scripts)
    {
        $this->scripts = $scripts;

        return $this;
    }

    /**
     * Fire install scripts
     * @param Command $command
     * @return bool
     */
    public function install(Command $command)
    {
        foreach ($this->scripts as $script) {
            try {
                $this->app->make($script)->fire($command);
            } catch (\Exception $e) {
                $command->error($e->getMessage());

                return false;
            }
        }

        return true;
    }
}
