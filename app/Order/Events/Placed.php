<?php

namespace App\Order\Events;

use App\Order\Entities\Entity;
use ArtisanSdk\Contract\Listener;
use ArtisanSdk\CQRS\Events\Event;

class Placed extends Event implements Listener
{
    /**
     * Prepare the event payload.
     *
     * @param \App\Order\Entities\Entity $entity
     */
    public function __construct(Entity $entity)
    {
        $this->entity = get_class($entity);
        foreach ($entity->toArray() as $key => $value) {
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
