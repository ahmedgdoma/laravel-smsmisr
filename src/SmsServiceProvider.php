<?php

namespace baklysystems\smsmisr;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $path = __DIR__;
        $this->publishes([
            $path. '/config/Sms.php'=> config_path('Sms.php'),
            $path.'/Http/controllers/SmsController.php' => app_path('Http/Controllers/SmsController.php'),
            $path.'/migrations/2017_11_12_123713_create_settings_table.php' =>base_path('database/migrations/2017_11_12_123713_create_settings_table.php'),
            $path.'/models/Systemconf.php' =>app_path('Systemconf.php'),

    ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('baklysystems\smsmisr\Sms');
    }
}
