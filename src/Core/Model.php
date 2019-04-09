<?php
/**
 * Project      tufu
 * @author      Giacomo Barbalinardo <info@ready24it.eu>
 * @copyright   2016
 */

namespace Tufu\Core;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Query\Builder;

/**
 * Class Model
 * @mixin Builder
 * @mixin EloquentModel
 */
class Model extends EloquentModel
{
    
}
