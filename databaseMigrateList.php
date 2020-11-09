<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;

class DbMigrateList
{

    static public function getDefaultDbPath($retun_folder_name = '')
    {
        $folder_name = 'common_db_path';
        if ($retun_folder_name != "") {
            return $folder_name;
        }
        return array(
            'folderName' => $folder_name,
            'fullPath' => '/database/migrations/' . $folder_name,
        );
    }

    static public function getDBNames($common_db = '')
    {
        $dbNames = ['db_test_1', 'db_test_2'];
        if ($common_db == 'default') {
            return 'common_db'; // COMMON DB
        }
        return $dbNames;
    }

    static public function ConfirmationMessage()
    {
        $message = 'Are you sure wish to continue? ' . "\r\n";
        $message .= ' 1 - Please make sure that all database have added into ' . __DIR__ . '\\databaseMigrateList.php ' . "\r\n";
        $message .= ' 2 - Please make sure to pass argument `--common_db=' . self::getDefaultDbPath('folder_name') . '` for the database `' . self::getDefaultDbPath('folder_name') . '`' . "\r\n";
        return  $message;
    }
    static public function ConfirmMigrationMessage()
    {
        //PATH =>C:\wamp64\www\laravel_test\blog\vendor\laravel\framework\src\Illuminate\Database\Console\Migrations
        $message = 'Are you sure wish to continue?. ' . "\r\n";
        $message .= ' Please make sure to pass argument `--path=/database/migrations/' . self::getDefaultDbPath('folder_name') . '` for the database `' . self::getDefaultDbPath('folder_name') . '` ' . "\r\n";
        return  $message;
    }

    static public function ConfirmRollBackApproved()
    {
        $sql = "SELECT * FROM rollback_approval WHERE status='A'";
        $result = DB::connection(self::getDBNames('default'))->select($sql);
        return $result;
    }

    static public function renameMigratedFile($path, $file)
    {
        $full_path = ltrim($path, "/") . '/' . $file . '.php';
        if (file_exists($full_path)) {
            $dbName = self::getDBNames('default');
            $file_content = file_get_contents($full_path);
            $file_replaced = str_replace("Schema::create(", "Schema::connection('" . $dbName . "')->create(", $file_content);
            $file_replaced = str_replace("Schema::dropIfExists(", "Schema::connection('" . $dbName . "')->dropIfExists(", $file_replaced);
            $down_replace = "//PLEASE MAKE SURE TO VERIFY `Schema::connection('" . $dbName . "')`\r\n\tpublic function down()";
            $up_replace = "//PLEASE MAKE SURE TO VERIFY `Schema::connection('" . $dbName . "')`\r\n\tpublic function up()";
            $file_replaced = str_replace("public function down()", $down_replace, $file_replaced);
            $file_replaced = str_replace("public function up()", $up_replace, $file_replaced);
            file_put_contents($full_path, $file_replaced);
        }
    }
}
