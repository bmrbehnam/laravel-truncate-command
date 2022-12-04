<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateSeededTablesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate database tables.';

    /**
     * Database tables
     * @var array
     */
    private array $tables;

    /**
     * @var string[]
     */
    private array $excludeTables = [
        'migrations',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->tables = [];
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->tables = DB::select('SHOW TABLES;');
        Schema::disableForeignKeyConstraints();

        foreach ($this->tables as $table) {
            $table = get_object_vars($table);
            $table = $table[key($table)];

            if (in_array($table, $this->excludeTables)) {
                continue;
            }

            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();
    }
}
