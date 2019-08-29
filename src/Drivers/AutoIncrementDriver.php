<?php

namespace Rorecek\AutoIncrement\Drivers;

interface AutoIncrementDriver
{
    public function get($table): int;

    public function set($table, $value): bool;

    public function maxValue($table, $primaryKey): int;

    public function hasTable($table): bool;
}
