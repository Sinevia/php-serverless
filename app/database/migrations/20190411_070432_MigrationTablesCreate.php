<?php

class MigrationTablesCreate
{

    public function up()
    {
        //\Sinevia\Migrate::setDatabase(db());
        \Sinevia\Migrate::createTables();
    }

    public function down()
    {
        //\Sinevia\Migrate::deleteTables();
    }
}
