<?php

namespace Rorecek\AutoIncrement;

use Rorecek\AutoIncrement\Drivers\AutoIncrementDriver;
use Rorecek\AutoIncrement\Drivers\MySqlDriver;

class AutoIncrement
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $connection;

    /**
     * @var AutoIncrementDriver
     */
    protected $driver;

    public function __construct()
    {
        $this->connection();
    }

    public function connection($connection = 'mysql'): AutoIncrement
    {
        $this->connection = $connection;

        $driver = config("database.connections.$connection.driver");

        if ($driver === 'mysql') {
            $this->driver = new MySqlDriver($connection);
        } else {
            throw new \RuntimeException("There is no AutoIncrement driver for connection: $connection");
        }

        return $this;
    }

    public function table($name): AutoIncrement
    {
        throw_unless(
            $this->driver->hasTable($name),
            \RuntimeException::class,
            'There is no table: ' . $name
        );

        $this->table = $name;

        return $this;
    }

    public function addRandomBetween(int $min, int $max): bool
    {
        return $this->add(random_int($min, $max));
    }

    public function add($value): bool
    {
        return $this->set($this->get() + $this->parseValue($value));
    }

    public function set($value, $primaryKey = 'id'): bool
    {
        $id = $this->parseValue($value);

        throw_if($id < 1, \RuntimeException::class, 'Auto-increment value must be positive');
        throw_if(
            $id < $this->minAllowedValue($primaryKey),
            \RuntimeException::class,
            'Auto-increment value must be equal or greater than ' . $this->minAllowedValue($primaryKey)
        );

        return $this->driver->set($this->getTable(), $id);
    }

    protected function parseValue($value): int
    {
        if ($value instanceof \Closure) {
            return $value();
        }

        if (is_array($value)) {
            return $value[array_rand($value)];
        }

        return $value;
    }

    protected function minAllowedValue($primaryKey = 'id'): int
    {
        return $this->driver->maxValue($this->getTable(), $primaryKey) + 1;
    }

    protected function getTable(): string
    {
        throw_unless(
            $this->table,
            \RuntimeException::class,
            'No table selected. You have to use table() method.'
        );

        return $this->table;
    }

    public function get(): int
    {
        return $this->driver->get($this->getTable());
    }

    public function reset($primaryKey = 'id'): bool
    {
        return $this->set($this->minAllowedValue($primaryKey));
    }

    public function getDriver(): AutoIncrementDriver
    {
        return $this->driver;
    }

}
