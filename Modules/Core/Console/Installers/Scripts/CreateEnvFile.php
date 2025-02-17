<?php

namespace Modules\Core\Console\Installers\Scripts;

use Illuminate\Console\Command;
use Modules\Core\Console\Installers\SetupScript;
use Modules\Core\Console\Installers\Writers\EnvFileWriter;

class CreateEnvFile implements SetupScript
{
    /**
     * @var EnvFileWriter
     */
    protected $env;
    /**
     * @var Command
     */
    protected $command;

    /**
     * @param Config $config
     * @param EnvFileWriter $env
     */
    public function __construct(EnvFileWriter $env)
    {
        $this->env = $env;
    }

    /**
     * Fire the install script
     * @param Command $command
     * @return mixed
     */
    public function fire(Command $command)
    {
        $this->command = $command;

        $this->env->create();

        $command->info('Successfully created ..env file');
    }
}
