<?php

namespace Modules\PublicAPI\Console;

use Illuminate\Console\Command;
use Modules\Core\Entities\AbstractDataTransferObject;
use Modules\PublicAPI\Domain\Application\Application;
use Modules\PublicAPI\Dto\CreateApplicationDto;
use Modules\PublicAPI\Dto\ApplicationDto;
use Modules\PublicAPI\Services\ApplicationService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

final class CreateApplication extends Command
{
    protected $signature = 'pa:make:app
        {name : Название приложения}
        {version : Версия приложения}
        {--status=active : Статус приложения}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание новое api-приложение';
    /**
     * @var ApplicationService
     */
    private $applicationService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->applicationService = app(ApplicationService::class);

        $application = $this->createApplication();

        $this->info('API-приложение успешно создано');
        $this->line('App key:    '.$application->key());
        $this->line('App secret: '.$application->secret());
        $this->line('Version:    '.$application->version());
        $this->line('Status:     '.$application->status());
    }

    private function createApplication(): Application
    {
        $name = $this->argument('name');
        $version = $this->argument('version');
        $status = $this->option('status') ?? null;

        $applicationDto = ApplicationDto::forCreating($name, $version, $status);

        return $this->applicationService->createApplication($applicationDto);
    }
}
