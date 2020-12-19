<?php   
//PATH = C:\wamp64\www\laravel_test\blog\vendor\laravel\framework\src\Illuminate\Database\Console\Migrations\MigrateMakeCommand.php
public function handle()
    {
        // It's possible for the developer to specify the tables to modify in this
        // schema operation. The developer may also specify if this table needs
        // to be freshly created so we can create the appropriate migrations.
        $ConfirmationMessage = DbMigrateList::ConfirmMigrationMessage();
        if ($ConfirmationMessage['validate']) {
            $validate_message = $this->confirm($ConfirmationMessage);
        } else {
            $validate_message = true;
        }
        if ($validate_message) {
            $name = Str::snake(trim($this->input->getArgument('name')));

            $table = $this->input->getOption('table');

            $create = $this->input->getOption('create') ?: false;

            if (!$table && is_string($create)) {
                $table = $create;

                $create = true;
            }

            if (!$table) {
                [$table, $create] = TableGuesser::guess($name);
            }

            // Now we are ready to write the migration out to disk. Once we've written
            // the migration out, we will dump-autoload for the entire framework to
            // make sure that the migrations are registered by the class loaders.
            $this->writeMigration($name, $table, $create);
            $InfoMigrateMessage = DbMigrateList::InfoMigrateMessage();
            if ($InfoMigrateMessage['display']) {
                $this->info($InfoMigrateMessage['message']); //NEW LINE FOR CONFIRM common_db
            }
            $this->composer->dumpAutoloads();
        }
    }
