<?php

namespace Modules\PublicAPI\Console;

use Illuminate\Console\Command;
use Modules\PublicAPI\Domain\Application\ApplicationKey;
use Modules\PublicAPI\Domain\PersonalToken;
use Modules\PublicAPI\Services\ApplicationService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

final class CreatePersonalToken extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'pa:make:token
        {appKey : App Key приложения, для которого необходимо создать токен}
        {--exp= : Дата/Время в формате strtotime после наступления которой токен будет считаться просроченным}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерация персонального аутентификационного токена для указанного приложения';
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

        $appKey = $this->argument('appKey');
        $expiredAt = $this->option('exp');
        if(!$expiredAt){
            $this->warn('Внимание! Будет создан токен без конечного времени использования');
        }

        $personalToken = $this->generateToken($appKey, $expiredAt);

        $this->info('Персональный токен успешно создан');
        $this->line('Token:      '.$personalToken);
        $this->line('Expired At: '.($personalToken->expiredAt()
            ? date('Y-m-d H:i:s', $personalToken->expiredAt())
            : 'Never'
        ));
    }

    private function generateToken(string $appKey, string $expiredAt = null): PersonalToken
    {
        $application = $this->applicationService->findApplicationOrFail($appKey);
        $application->addPersonalToken(
            $personalToken =$this->applicationService->generateJWTPersonalToken(
                $application,
                $expiredAt ? strtotime($expiredAt) : null
            )
        );

        $this->applicationService->saveApplication($application);

        return $personalToken;
    }
}
