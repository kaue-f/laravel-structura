<?php

namespace KaueF\Structura\Console\Concerns;

use Illuminate\Support\Facades\Process;

trait InteractsWithCreate
{
    /**
     * Run composer dump-autoload
     */
    protected function runDumpAutoload(): void
    {
        (app()->environment('production'))
            ? $this->comment("\n".'ℹ️ Run "composer dump-autoload" if needed.')
            : Process::run('composer dump-autoload')->output();
    }

    protected function optionOrConfig(string $key, string $option, bool $default = false): bool
    {
        if ($this->option($option)) {
            return true;
        }

        if (config()->has("structura.default_options.{$key}.{$option}")) {
            return (bool) config("structura.default_options.{$key}.{$option}", $default);
        }

        return $default;
    }

    protected function optionValueOrConfig(string $key, string $option, ?string $default = null): ?string
    {
        $value = $this->option($option);

        if ($value !== null) {
            return $value;
        }

        if (config()->has("structura.default_options.{$key}.{$option}")) {
            return config("structura.default_options.{$key}.{$option}", $default);
        }

        return $default;
    }
}
