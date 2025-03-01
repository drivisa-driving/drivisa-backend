<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Modules\Core\Console\Installers\Installer;
use Modules\Core\Console\Installers\Scripts\ConfigureAppUrl;
use Modules\Core\Console\Installers\Scripts\ConfigureDatabase;
use Modules\Core\Console\Installers\Scripts\ConfigureUserProvider;
use Modules\Core\Console\Installers\Scripts\CreateEnvFile;
use Modules\Core\Console\Installers\Scripts\ModuleAssets;
use Modules\Core\Console\Installers\Scripts\ModuleMigrator;
use Modules\Core\Console\Installers\Scripts\ModuleSeeders;
use Modules\Core\Console\Installers\Scripts\ProtectInstaller;
use Modules\Core\Console\Installers\Scripts\SetAppKey;
use Modules\Core\Console\Installers\Scripts\SetInstalledFlag;
use Modules\Core\Console\Installers\Scripts\UnignoreComposerLock;
use Modules\Core\Console\Installers\Scripts\UnignorePackageLock;
use Modules\Core\Console\Installers\Traits\BlockMessage;
use Modules\Core\Console\Installers\Traits\SectionMessage;
use Symfony\Component\Console\Input\InputOption;

class InstallCommand extends Command
{
    use BlockMessage, SectionMessage;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ceo:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install CodeEcho CMS';

    /**
     * @var Installer
     */
    private $installer;

    /**
     * Create a new command instance.
     *
     * @param Installer $installer
     * @internal param Filesystem $finder
     * @internal param Application $app
     * @internal param Composer $composer
     */
    public function __construct(Installer $installer)
    {
        parent::__construct();
        $this->getLaravel()['.env'] = 'local';
        $this->installer = $installer;
    }

    /**
     * Execute the actions
     *
     * @return mixed
     */
    public function handle()
    {
        $this->blockMessage('Welcome!', 'Starting the installation process...', 'comment');

        $success = $this->installer->stack([
            ProtectInstaller::class,
            CreateEnvFile::class,
            ConfigureDatabase::class,
            ConfigureAppUrl::class,
            SetAppKey::class,
            ConfigureUserProvider::class,
            ModuleMigrator::class,
            ModuleSeeders::class,
            ModuleAssets::class,
            UnignoreComposerLock::class,
            UnignorePackageLock::class,
            SetInstalledFlag::class,
        ])->install($this);

        if ($success) {
            $this->info('Platform ready!');
        }
    }

    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force the installation, even if already installed'],
        ];
    }
}
