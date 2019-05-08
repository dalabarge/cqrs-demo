<?php

namespace App\Order\Events;

use App\Order\Entities\Entity;
use ArtisanSdk\Contract\Listener;
use ArtisanSdk\CQRS\Events\Event;

class Placing extends Event implements Listener
{
    /**
     * Prepare the event payload.
     *
     * @param array $arguments
     */
    public function __construct(array $arguments = [])
    {
        $this->entity = Entity::class;
        foreach ($arguments as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Get the listeners this event subscribes to.
     *
     * @return array
     */
    public static function listeners()
    {
        return [];
    }
}
