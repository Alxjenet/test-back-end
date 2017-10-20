<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    protected $dbConnection;

    /**
     * DatabaseSeeder constructor.
     */
    public function __construct()
    {
        $this->dbConnection = Config::get('database.default');
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->setDisabledForeignKey();
        DB::table('aircrafts')->truncate();
        DB::table('airports')->truncate();
        $this->setEnabledForeignKey();

        DB::statement(file_get_contents(database_path('seeds/airports.sql')));
        DB::statement(file_get_contents(database_path('seeds/aircrafts.sql')));
    }

    /**
     * Disabled all foreign keys
     */
    protected function setDisabledForeignKey() :void
    {
        switch($this->dbConnection) {
            case 'sqlite':
                DB::statement('PRAGMA foreign_keys = OFF');
                break;
            case 'mysql':
            default :
                DB::statement('SET FOREIGN_KEY_CHECKS = 0');
                break;
        }
    }


    /**
     * Enabled all foreign keys
     */
    protected function setEnabledForeignKey() :void
    {
        switch($this->dbConnection) {
            case 'sqlite':
                DB::statement('PRAGMA foreign_keys = ON');
                break;
            case 'mysql':
            default :
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
                break;
        }
    }


}
