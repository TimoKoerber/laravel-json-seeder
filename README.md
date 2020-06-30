## Laravel JSON Seeder

This packages makes creating and working with JSON seeds a breeze. You can...
1. Create JSON seeds from your database
2. Seed your database with these JSON files

## Installation

**Require this package** with composer. It is recommended to only require the package for development.

```shell
composer require timokoerber/laravel-json-seeder --dev
```

Next you need to **publish** the config file and register the required commands with ...   

```shell
php artisan vendor:publish --provider="TimoKoerber\LaravelJsonSeeder\JsonSeederServiceProvider"
```

This will create the file `config/jsonseeder.php` where you can find the configurations.

Next add the JsonSeederServiceProvider to the `providers` array in `config/app.php` ...   

```php
// config/app.php

'providers' => [
    ...
    
    TimoKoerber\LaravelJsonSeeder\JsonSeederServiceProvider::class,
    
    ...
]
```

## Creating JSON seeds from database
![Laravel JSON Seeder - Creating JSON seeds from database](https://user-images.githubusercontent.com/65356688/86143845-3ceadc00-baf5-11ea-956f-d707b88d148c.gif)

Of course you can create the JSON files manually. But if you already have a good development database, you can easily export it into JSON seeds. 

You can create seeds for **every table in your database** by calling ...

```shell
php artisan jsonseeds:create
```
This will create one JSON file for wach table in your database (i.e. table *users* -> *users.json*). 

If you only want to create a seed of **one specific table** (i.e. `users`), call ...

```shell
php artisan jsonseeds:create users
```

Existing files **won't be overwritten** by default. If you call the command again, a **sub-directory will be created** and the JSON seeds will be stored there. 
If you want to **overwrite the existing seeds**, use the `overwrite` option like ...

```shell
php artisan jsonseeds:create users -o|--overwrite
```

or just **use the command** ...

```shell
php artisan jsonseeds:overwrite users
```

From now on you can work with

## Seeding

![Laravel JSON Seeder - Seeding](https://user-images.githubusercontent.com/65356688/86143769-23e22b00-baf5-11ea-90e6-0631a41d81c4.gif)

Go to your `databas/seeds/DatabaseSeeder.php` and add the JsonSeeder inside the `run()` method like this ...

```php
// database/seeds/DatabaseSeeder.php

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(TimoKoerber\LaravelJsonSeeder\JsonDatabaseSeeder::class);
    }
}
```

You can now call the JSON Seeder with the **usual Artisan command** ...

```shell
php artisan db:seed
```

## Settings & Configurations

### Directory

By default your seeds will be written into or read from the directory `/database/json`. If you want a different directory, you can add the environment variable 
`JSON_SEEDS_DIRECTORY` in your `.env` file ...

```
# .env

JSON_SEEDS_DIRECTORY=database/json
```

### Ignoring tables

Some tables in your database might not require any seeds. 
If you want to ignore these tables, you can put them into the setting `ignore-tables` in the `/config.jsonseeder.php`

```php
// config/jsonseeder.php

'ignore-tables' => [
    'migrations',
    'failed_jobs',
    'password_resets',
]
```

If a table in your database is empty, the LaravelJsonSeeder will create a JSON file with an empty array by default. This might be useful if you want your seeds to truncate this table.
If you don't want this, you can change the setting `ignore-empty-tables` in `config/jsonseeder.php`, so no JSON seed will be created.

```php
// config/jsonseeder.php

'ignore-empty-tables' => true
```

> **Important!!!** Do not forget to clear the cache after editing the config file: `php artisan cache:clear`

### Environments

The environment variable `JSON_SEEDS_DIRECTORY` might be useful if you are using seeds in Unit Tests and want to use different seeds for this. 

```
- database
  - json
      - development
        - comapnies.json
        - users.json 
        - posts.json
      - testing
        - users.json
        - posts.json
```
#### Development
```
# .env

JSON_SEEDS_DIRECTORY=database/json/development
```
#### Testing
```
# .env

JSON_SEEDS_DIRECTORY=database/json/testing
```

## Errors & Warnings

![jsonseeder-errors](https://user-images.githubusercontent.com/65356688/86142165-2e9bc080-baf3-11ea-99b8-9bef46cd61f2.gif)


| Error | Type | Solution |
| ------| -----| -------- |
| Table does not exist! | Error | The name of the JSON file does not match any table. Check the filename or create the table. |
| JSON syntax is invalid! | Error | The JSON text inside the file seems to be invalid. Check if the JSON format is correct.|
| JSON file is empty! | Error | The JSON file is completely empty. Maybe you should delete the file if it is not required.|
| Exception occured! Check logfile! | Error | There seems to be a problem with the Database. Check your system and configuration. |
| JSON file has no rows! | Warning | The JSON fail contains only an empty array `[]`. This results in a truncated table and might be intended. |
| Missing fields! | Warning | At least one row in the JSON file is missing a field, that is present in the database table. Check for typos or provide it in the JSON file. |
| Unknown fields! | Warning | At least one row in the JSON file has a field that does not exist in the database. Check for typos or make sure to add it to the database table. |
