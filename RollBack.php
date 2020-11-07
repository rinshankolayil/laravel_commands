<?php

namespace App\Console\Commands;

use App\Libraries\ASJ;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class RollBack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rollback:all {step}';

    protected $params = array();

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'IT WILL ROLL BACK ALL THE LAST CREATED MIGRATIONS, PASS A PARAMETER NAMED AS STEP WITH 0,1,2 
    TO ROLL BACK THE MIGRATIONS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $arguments = $this->arguments();
        $step = $arguments['step'];
        if ($step == 0) {
            $step_command = "";
        } else {
            $step_command = (int) $step;
        }
        $dbNameExist = DbMigrateList::getDBNames();
        $ConfirmationMessage = DbMigrateList::ConfirmationMessage();
        if ($this->confirm($ConfirmationMessage)) {
            foreach (Config::get('database.connections') as $name => $details) {
                if (in_array($name, $dbNameExist)) {
                    $this->info('Running rollback for "' . $name . '"');
                    if ($step == 0) {
                        $this->call('migrate:rollback', array(
                            '--database' => $name,
                        ));
                    } else {
                        $this->call('migrate:rollback --step=' . $step_command, array(
                            '--database' => $name,
                        ));
                    }
                }
            }
        }
    }
}
