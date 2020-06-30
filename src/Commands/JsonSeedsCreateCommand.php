<?php

namespace TimoKoerber\LaravelJsonSeeder\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class JsonSeedsCreateCommand extends Command
{
    protected $signature = 'jsonseeds:create
    {table? : Name of the table to create a seeder from }
    {--o|overwrite : overwrite existing seeder files }';

    protected $description = 'Create a seeder file from a database table';

    protected $tableName = null;

    protected $seedsDirectory = '/database/json';

    protected $overwriteExistingFiles = false;

    protected $tablesToIgnore = [];

    protected $ignoreEmptyTables = true;

    public function __construct()
    {
        parent::__construct();

        $this->seedsDirectory = config('jsonseeder.directory', '/database/json');
        $this->tablesToIgnore = config('jsonseeder.ignore-tables', []);
        $this->ignoreEmptyTables = config('jsonseeder.ignore-empty-tables', false);
    }

    public function handle()
    {
        $this->tableName = $this->argument('table');
        $this->overwriteExistingFiles = $this->option('overwrite');

        $this->process();
    }

    protected function process()
    {
        $this->line('<info>Environment:</info> '.env('APP_ENV'));
        $this->line('<info>Overwrite existing files:</info> '.($this->overwriteExistingFiles ? 'Yes' : 'No'));

        try {
            $this->createJsonFiles();
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function createJsonFiles(): bool
    {
        $tablesToExport = $this->getTablesToExport();

        $seedsDirectory = $this->seedsDirectory.'/';
        $basePath = base_path($this->seedsDirectory).'/';

        $FileSystem = new Filesystem();

        if (! $FileSystem->exists($basePath)) {
            File::makeDirectory($basePath, 0755, true);
        }

        if ($this->tableName) {
            $filename = $this->tableName.'.json';
            $existingsFiles = $FileSystem->exists($basePath.$filename);
        } else {
            $existingsFiles = (bool) $FileSystem->files($basePath);
        }

        // create sub-directory so existing files won't be overwritten
        if ($existingsFiles && ! $this->overwriteExistingFiles) {
            $directory = now()->toDateTimeString();
            $basePath .= $directory.'/';
            $seedsDirectory .= $directory.'/';
            File::makeDirectory($basePath, 0755, true);
        }

        $this->line('');

        foreach ($tablesToExport as $tableName) {
            $this->line('Create seeds for table '.$tableName);
            $content = DB::table($tableName)->select()->get();
            $rowsCount = count($content);

            if ($this->ignoreEmptyTables && $rowsCount === 0) {
                $this->outputWarning('No JSON file was created, because table was empty.');
                continue;
            }

            $filename = $tableName.'.json';

            try {
                File::put($basePath.$filename, $content->toJson(JSON_PRETTY_PRINT));
                $this->outputInfo('Created '.$seedsDirectory.$filename.' ('.$rowsCount.' rows)');
            } catch (\Exception $e) {
                Log::alert($e->getMessage());
            }
        }

        return true;
    }

    protected function getTablesToExport()
    {
        if ($this->tableName) {
            if (! Schema::hasTable($this->tableName)) {
                throw new \Exception('Given table '.$this->tableName.' does not exist.');
            }

            return [$this->tableName];
        }

        $tables = $this->getDatabaseTables();

        if ($this->tablesToIgnore) {
            $this->line('<info>Ignore tables:</info> '.implode(', ', $this->tablesToIgnore));
            $tables = array_diff($tables, $this->tablesToIgnore);
        }

        if (! $tables) {
            throw new \Exception('No Database tables found.');
        }

        return $tables;
    }

    protected function getDatabaseTables(): array
    {
        $tables = DB::select('SHOW TABLES');

        return array_map(static function($element) {
            $array = (array) $element;
            return array_pop($array);
        }, $tables);
    }

    protected function outputInfo(string $message): void
    {
        $this->info(' > '.$message);
    }

    protected function outputWarning(string $message): void
    {
        $this->warn(' > '.$message);
    }

    protected function outputError(string $message): void
    {
        $this->error(' > '.$message);
    }
}
