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

    static public function getDBNames($common_db = '')
    {
        $dbNames = [];
        $default_db = "";
        foreach (Config::get('database.connections') as $name => $details) {
            if ((!isset($details['add_to_migrate']) || (isset($details['add_to_migrate']) && $details['add_to_migrate'] == true))) {
                if ($details['is_a_company'] == "yes") {
                    array_push($dbNames, $name);
                } else if ($details['is_a_company'] == "default") {
                    $default_db = $name;
                }
            }
        }

        if ($common_db == 'default') {
            return $default_db; // COMMON DB
        }
        return $dbNames;
    }
