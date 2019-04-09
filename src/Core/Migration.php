<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2016
 */

namespace Tufu\Core;

use Closure;
use Illuminate\Database\Migrations\Migration as BaseMigration;
use Illuminate\Database\Capsule\Manager as Capsule;

abstract class Migration extends BaseMigration
{
    public abstract function up();
    public abstract function down();

    protected function create($table, Closure $callback)
    {
        return Capsule::schema()->create($table, $callback);
    }

    protected function drop($table)
    {
        return Capsule::schema()->drop($table);
    }
}