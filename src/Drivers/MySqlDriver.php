<?php

namespace Rorecek\AutoIncrement\Drivers;

use Illuminate\Support\Facades\DB;

class MySqlDriver implements AutoIncrementDriver
{
    protected $connection;
    protected $database;

    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->database = config("database.connections.$connection.database");
    }

    public function get($table): int
    {
        $result = DB::connection($this->connection)
            ->select(
                'SELECT AUTO_INCREMENT FROM information_schema.TABLES'
                . ' WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?',
                [$this->database, $table]
            );

        if (isset($result[0], $result[0]->AUTO_INCREMENT)) {
            return $result[0]->AUTO_INCREMENT;
        }

        throw new \RuntimeException("Can't get auto-increment value for table: " . $table);
    }

    public function maxValue($table, $primaryKey = 'id'): int
    {
        return (int) DB::connection($this->connection)->table($table)->max($primaryKey);

    }

    public function set($table, $value): bool
    {
        return DB::connection($this->connection)->statement("ALTER TABLE $table AUTO_INCREMENT=$value");
    }

    public function hasTable($table): bool
    {
        return DB::connection($this->connection)->getSchemaBuilder()->hasTable($table);
    }
}
