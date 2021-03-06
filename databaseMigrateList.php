<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DbMigrateList
{

    static public function getDefaultDbPath($retun_folder_name = '')
    {
        $folder_name = 'common_db_migrations';
        if ($retun_folder_name != "") {
            return $folder_name;
        }
        return array(
            'folderName' => $folder_name,
            'fullPath' => '/database/migrations/' . $folder_name,
        );
    }

    static public function getDBNames($return_type = '')
    {
        $dbNames = [];
        $dbAll = [];
        $default_db = "";
        foreach (Config::get('database.connections') as $name => $details) {
            if (isset($details['add_to_migrate']) && $details['add_to_migrate'] == true) {
                if ($details['key_1_test'] == "yes") {
                    array_push($dbNames, $name);
                    $dbAll['key_1_test'][] = $name;
                } else if ($details['key_1_test'] == "default") {
                    $default_db = $name;
                    $dbAll['default'][] = $name;
                }
            }
        }

        if ($return_type == 'default') {
            return $default_db;
        } else if ($return_type == "all") {
            return $dbAll;
        } else {
            return $dbNames;
        }
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
        return  [
            'message' => $message,
            'validate' => true
        ];
    }

    static public function InfoMigrateMessage()
    {
        //PATH =>C:\wamp64\www\laravel_test\blog\vendor\laravel\framework\src\Illuminate\Database\Console\Migrations
        $message = "Please make sure to add `Schema::connection('common_db')->create(` for the DB `common_db`";
        return [
            'message' => $message,
            'display' => true
        ];
    }

    static public function ConfirmRollBackApproved()
    {
        $count = DB::connection(self::getDBNames('default'))
            ->table('rollback_approval_dummy_table')
            ->where('status', 'A')
            ->where('pc_user_name_env', 'username')
            ->count();
        return $count;
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
