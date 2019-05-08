<?php

namespace App\Order\Entities;

use Illuminate\Support\Fluent;

/**
 * Order Entity.
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property int    $total
 */
class Entity extends Fluent
{
}
