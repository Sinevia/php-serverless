<?php

class TablesUserCreate
{
    public function up()
    {
        \App\Plugins\UserPlugin::createTables();
    }
}