<?php

namespace TimoKoerber\LaravelJsonSeeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use TimoKoerber\LaravelJsonSeeder\Utils\SeederResult;
use TimoKoerber\LaravelJsonSeeder\Utils\SeederResultTable;

class JsonDatabaseSeeder extends Seeder
{
    protected $tableName;

    /**
     * @var SeederResultTable
     */
    protected $SeederResultTable;

    public function run()
    {
        $env = App::environment();
        $this->command->line('<info>Environment:</info> '.$env);

        $seedsDirectory = config('jsonseeder.directory', '/database/json');
        $absoluteSeedsDirectory = base_path($seedsDirectory);

        if (! File::isDirectory($absoluteSeedsDirectory)) {
            $this->command->error('The directory '.$seedsDirectory.' was not found.');

            return false;
        }

        $this->command->line('<info>Directory:</info> '.$seedsDirectory);

        $jsonFiles = $this->getJsonFiles($absoluteSeedsDirectory);

        if (! $jsonFiles) {
            $this->command->warn('The directory '.$seedsDirectory.' has no JSON seeds.');
            $this->command->line('You can create seeds from you database by calling <info>php artisan jsonseeds:create</info>');

            return false;
        }

        $this->command->line('Found <info>'.count($jsonFiles).' JSON files</info> in <info>'.$seedsDirectory.'</info>');
        $this->SeederResultTable = new SeederResultTable();

        $this->seed($jsonFiles);

        return true;
    }

    public function seed(array $jsonFiles)
    {
        Schema::disableForeignKeyConstraints();

        foreach ($jsonFiles as $jsonFile) {
            $SeederResult = new SeederResult();
            $this->SeederResultTable->addRow($SeederResult);

            $filename = $jsonFile->getFilename();
            $tableName = Str::before($filename, '.json');
            $SeederResult->setFilename($filename);
            $SeederResult->setTable($tableName);

            $this->command->line('Seeding '.$filename);

            if (! Schema::hasTable($tableName)) {
                $this->outputError(SeederResult::ERROR_NO_TABLE);

                $SeederResult->setStatusAborted();
                $SeederResult->setError(SeederResult::ERROR_NO_TABLE);
                $SeederResult->setTableStatus(SeederResult::TABLE_STATUS_NOT_FOUND);

                continue;
            }

            $SeederResult->setTableStatus(SeederResult::TABLE_STATUS_EXISTS);

            $filepath = $jsonFile->getRealPath();
            $content = File::get($filepath);
            $jsonArray = $this->getValidJsonString($content, $SeederResult);

            // empty array is a valid result, check for null
            if ($jsonArray === null) {
                continue;
            }

            DB::table($tableName)->truncate();

            if (empty($jsonArray)) {
                $this->outputWarning(SeederResult::ERROR_NO_ROWS);
                $SeederResult->setError(SeederResult::ERROR_NO_ROWS);

                continue;
            }

            $tableColumns = DB::getSchemaBuilder()->getColumnListing($tableName);

            foreach ($jsonArray as $data) {
                $this->compareJsonWithTableColumns($data, $tableColumns, $SeederResult);
                $data = Arr::only($data, $tableColumns);

                try {
                    DB::table($tableName)->insert($data);
                    $SeederResult->addRow();
                    $SeederResult->setStatusSucceeded();
                } catch (\Exception $e) {
                    $this->outputError(SeederResult::ERROR_EXCEPTION);
                    $SeederResult->setError(SeederResult::ERROR_EXCEPTION);
                    $SeederResult->setStatusAborted();
                    Log::warn($e->getMessage());
                    break;
                }
            }

            $this->outputInfo('Seeding successful!');
        }

        Schema::enableForeignKeyConstraints();

        $this->command->line('');
        $this->command->table($this->SeederResultTable->getHeader(), $this->SeederResultTable->getResult());
    }

    protected function getJsonFiles($seedsDirectory)
    {
        $files = File::files($seedsDirectory);

        $files = array_filter($files, static function ($filename) {
            return Str::endsWith($filename, 'json');
        });

        return array_values($files);
    }

    protected function compareJsonWithTableColumns(array $item, array $columns, SeederResult $SeederResult)
    {
        $diff = array_diff($columns, array_keys($item));

        if ($diff) {
            $SeederResult->setError(SeederResult::ERROR_FIELDS_MISSING.' '.implode(',', $diff));
        }

        $diff = array_diff(array_keys($item), $columns);

        if ($diff) {
            $SeederResult->setError(SeederResult::ERROR_FIELDS_UNKNOWN.' '.implode(',', $diff));
        }
    }

    protected function getValidJsonString($content, SeederResult $SeederResult)
    {
        if (empty($content)) {
            $this->outputError(SeederResult::ERROR_FILE_EMPTY);
            $SeederResult->setError(SeederResult::ERROR_FILE_EMPTY);
            $SeederResult->setStatusAborted();

            return null;
        }

        $jsonContent = json_decode($content, true);

        if (json_last_error()) {
            $this->outputError(SeederResult::ERROR_SYNTAX_INVALID);
            $SeederResult->setError(SeederResult::ERROR_SYNTAX_INVALID);
            $SeederResult->setStatusAborted();

            return null;
        }

        $SeederResult->setStatusSucceeded();

        return $jsonContent;
    }

    protected function outputInfo(string $message)
    {
        $this->command->info(' > '.$message);
    }

    protected function outputWarning(string $message)
    {
        $this->command->warn(' > '.$message);
    }

    protected function outputError(string $message)
    {
        $this->command->error(' > '.$message);
    }
}
