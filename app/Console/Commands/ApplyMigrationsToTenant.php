<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Modules\Tenants\App\Models\Tenant;

class ApplyMigrationsToTenant extends Command
{
    protected $signature = 'tenancy:migrate-tenant {tenant}';
    protected $description = 'Apply all module tenant migrations to a single tenant database';

    public function handle()
    {
        $tenantId = $this->argument('tenant');

        // Retrieve tenant
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("❌ Tenant '{$tenantId}' not found.");
            return;
        }

        // Get all tenant migration paths from modules
        $modulePaths = $this->getAllTenantMigrationPaths();
        if (empty($modulePaths)) {
            $this->warn('⚠️ No tenant migration paths found.');
            return;
        }

        $this->info("🔄 Running migrations for tenant: {$tenant->id}");
        tenancy()->initialize($tenant);

        foreach ($modulePaths as $path) {
            $this->call('migrate', [
                '--path' => $path,
                '--force' => true,
            ]);
        }

        tenancy()->end();
        $this->info("✅ Migrations complete for tenant: {$tenant->id}");
    }

    protected function getAllTenantMigrationPaths(): array
    {
        $basePath = base_path('Modules');
        $paths = [];

        foreach (File::directories($basePath) as $modulePath) {
            $tenantMigrationPath = $modulePath . '/Database/migrations';

            if (File::isDirectory($tenantMigrationPath)) {
                $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $tenantMigrationPath);
                $paths[] = $relativePath;
            }
        }

        return $paths;
    }
}
