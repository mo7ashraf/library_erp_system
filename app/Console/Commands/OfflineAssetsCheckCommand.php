<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OfflineAssetsCheckCommand extends Command
{
    protected $signature = 'erp:check-offline-assets';

    protected $description = 'Checks that ERP views do not depend on external internet assets.';

    private int $errors = 0;

    public function handle(): int
    {
        $this->info('Starting offline assets check...');
        $this->line('------------------------------------------');

        $this->checkLocalCairoFonts();
        $this->checkViewsForExternalAssets();

        $this->line('------------------------------------------');

        if ($this->errors > 0) {
            $this->error("Offline assets check failed with {$this->errors} error(s).");

            return self::FAILURE;
        }

        $this->info('✓ Offline assets check passed.');

        return self::SUCCESS;
    }

    private function checkLocalCairoFonts(): void
    {
        $this->info('Checking local Cairo font files...');

        $requiredFonts = [
            'public/assets/fonts/cairo/Cairo-Regular.ttf',
            'public/assets/fonts/cairo/Cairo-Medium.ttf',
            'public/assets/fonts/cairo/Cairo-SemiBold.ttf',
            'public/assets/fonts/cairo/Cairo-Bold.ttf',
            'public/assets/fonts/cairo/Cairo-ExtraBold.ttf',
            'public/assets/fonts/cairo/Cairo-Black.ttf',
        ];

        foreach ($requiredFonts as $path) {
            if (! File::exists(base_path($path))) {
                $this->errors++;
                $this->error("✗ Missing font file: {$path}");

                continue;
            }

            $this->line("✓ Font exists: {$path}");
        }
    }

    private function checkViewsForExternalAssets(): void
    {
        $this->info('Checking Blade views for external internet assets...');

        $patterns = [
            'fonts.googleapis.com',
            'fonts.gstatic.com',
            'cdn.jsdelivr.net',
            'cdnjs.cloudflare.com',
            'unpkg.com',
            'code.jquery.com',
            'ajax.googleapis.com',
            'stackpath.bootstrapcdn.com',
            'maxcdn.bootstrapcdn.com',
        ];

        $viewPath = resource_path('views');

        if (! File::exists($viewPath)) {
            $this->errors++;
            $this->error('✗ resources/views path does not exist.');

            return;
        }

        foreach (File::allFiles($viewPath) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $content = File::get($file->getPathname());

            foreach ($patterns as $pattern) {
                if (str_contains($content, $pattern)) {
                    $this->errors++;
                    $this->error("✗ External asset found in {$relativePath}: {$pattern}");
                }
            }
        }

        if ($this->errors === 0) {
            $this->line('✓ No external internet asset references found in Blade views.');
        }
    }
}