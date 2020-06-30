<?php

namespace TimoKoerber\LaravelJsonSeeder\Commands;

use Illuminate\Console\Command;

class JsonSeedsOverwriteCommand extends JsonSeedsCreateCommand
{
    protected $signature = 'jsonseeds:overwrite
    {table? : Name of the table to create a seeder from }';

    protected $description = 'Command description';

    public function handle()
    {
        $this->tableName = $this->argument('table');
        $this->overwriteExistingFiles = true;

        $this->process();
    }
}
