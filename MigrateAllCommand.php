<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class MigrateAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:all {--common_db=}';
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $arguments = $this->arguments();
        $common_db = "";
        if (isset($arguments['common_db'])) {
            $common_db = $arguments['common_db'];
        }

        $dbNameExist = DbMigrateList::getDBNames();
        $ConfirmationMessage = DbMigrateList::ConfirmationMessage();
        if ($this->confirm($ConfirmationMessage)) {
            foreach (Config::get('database.connections') as $name => $details) {
                if ((in_array($name, $dbNameExist) && $common_db == "") || ($common_db == "common_db" && $name == $common_db)) {
                    $this->info('Running migration for "' . $name . '"');
                    $this->call('migrate', array(
                        '--database' => $name,
                        '--path=/database/migrations/' . $common_db,
                    ));
                }
            }
        }
    }
}
