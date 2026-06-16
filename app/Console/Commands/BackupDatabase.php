<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    protected $description = 'Backup the database';

    public function handle()
    {
        $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
        
        // Ensure directory exists
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        $sqlContent = "-- Billing System Database Backup\n";
        $sqlContent .= "-- Generated: " . now()->toDateTimeString() . "\n\n";
        $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        // Get all tables
        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {
            $tableArray = (array) $table;
            $tableName = reset($tableArray);

            // Generate DROP and CREATE TABLE statement
            $showCreate = DB::select("SHOW CREATE TABLE `{$tableName}`");
            if (!empty($showCreate)) {
                $createTableSql = $showCreate[0]->{'Create Table'};
                $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sqlContent .= $createTableSql . ";\n\n";
            }

            // Generate INSERT statements
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $columns = implode('`, `', array_keys($rowArray));
                    
                    $values = array_map(function($val) {
                        if (is_null($val)) {
                            return 'NULL';
                        }
                        return DB::getPdo()->quote($val);
                    }, array_values($rowArray));

                    $valuesStr = implode(', ', $values);
                    $sqlContent .= "INSERT INTO `{$tableName}` (`{$columns}`) VALUES ({$valuesStr});\n";
                }
                $sqlContent .= "\n";
            }
        }

        $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";

        // Save file in backups directory in storage disk
        Storage::put('backups/' . $filename, $sqlContent);

        $this->info('Database backup created: ' . $filename);
    }
}
