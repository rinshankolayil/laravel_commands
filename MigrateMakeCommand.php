    public function handle()
    {
        $ConfirmationMessage = DbMigrateList::ConfirmMigrationMessage(); 
        if ($this->confirm($ConfirmationMessage)) {
            // It's possible for the developer to specify the tables to modify in this
            // schema operation. The developer may also specify if this table needs
            // to be freshly created so we can create the appropriate migrations.
            $name = Str::snake(trim($this->input->getArgument('name')));

            $table = $this->input->getOption('table');

            $create = $this->input->getOption('create') ?: false;

            // If no table was given as an option but a create option is given then we
            // will use the "create" option as the table name. This allows the devs
            // to pass a table name into this option as a short-cut for creating.
            if (!$table && is_string($create)) {
                $table = $create;

                $create = true;
            }

            // Next, we will attempt to guess the table name if this the migration has
            // "create" in the name. This will allow us to provide a convenient way
            // of creating migrations that create new tables for the application.
            if (!$table) {
                [$table, $create] = TableGuesser::guess($name);
            }

            // Now we are ready to write the migration out to disk. Once we've written
            // the migration out, we will dump-autoload for the entire framework to
            // make sure that the migrations are registered by the class loaders.
            $this->writeMigration($name, $table, $create);
            $this->info("Please make sure to add `Schema::connection('db_auth')->create(` for the DB `db_auth`");
            $this->composer->dumpAutoloads();
        }
    }
