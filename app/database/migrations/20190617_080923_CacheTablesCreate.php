<?php

class CacheTablesCreate
{

    public function up()
    {
        \Sinevia\Cache::tableCreate();
    }

    public function down()
    {
        \Sinevia\Cache::tableDelete();
    }
}
