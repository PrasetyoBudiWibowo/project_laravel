<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';

    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');
        $servicePath = app_path('Services/' . $name . '.php');

        if (File::exists($servicePath)) {
            $this->error('Service already exists!');
            return;
        }

        $stub = "<?php

namespace App\Services;

class {$name}
{
    //
}
";
        File::ensureDirectoryExists(app_path('Services'));
        File::put($servicePath, $stub);
        $this->info("Service {$name} created successfully.");
    }
}
