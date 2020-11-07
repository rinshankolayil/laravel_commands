<?php

namespace App\Console\Commands;

class DbMigrateList
{

    static public function getDBNames()
    {
        $dbNames = ['db_test_1', 'db_test_2'];
        return $dbNames;
    }

    static public function ConfirmationMessage()
    {
        $message = 'Are you sure wish to continue? ' . "\r\n";
        $message .= ' 1 - Please make sure that all database have added into ' . __DIR__ . '\\databaseMigrateList.php ' . "\r\n";
        $message .= ' 2 - Please make sure to pass argument `--common_db=db_auth` for the database `db_common`' . "\r\n";
        return  $message;
    }
    static public function ConfirmMigrationMessage()
    {
        //PATH =>C:\wamp64\www\laravel_test\blog\vendor\laravel\framework\src\Illuminate\Database\Console\Migrations
        $message = 'Are you sure wish to continue?. ' . "\r\n";
        $message .= ' Please make sure to pass argument `--path=/database/migrations/db_common` for the database `db_common` ' . "\r\n";
        return  $message;
    }
}
