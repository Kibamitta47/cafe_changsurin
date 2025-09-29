<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // เปิดได้เฉพาะ local หรือเมื่อระบุให้เปิดอย่างชัดเจนผ่าน config/env
        $enabled = (bool) (config('telescope.enabled') ?? env('TELESCOPE_ENABLED', false));

        if (!app()->environment('local') || !$enabled) {
            // ป้องกันไม่ให้มีการอัดข้อมูลหรือเขียน DB ใด ๆ บนโปรดักชัน
            if (class_exists(Telescope::class)) {
                Telescope::stopRecording();
                Telescope::filter(fn () => false);
            }
            return;
        }

        // ==== ด้านล่างนี้จะทำงานเฉพาะใน local/dev เท่านั้น ====

        // Telescope::night(); // เปิด dark theme ได้ถ้าต้องการ

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return $isLocal ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }
}
