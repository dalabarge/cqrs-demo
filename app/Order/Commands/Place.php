<?php

namespace App\Order\Commands;

use App\Order\Entities\Entity;
use App\Order\Events\Placed;
use App\Order\Events\Placing;
use ArtisanSdk\Contract\Eventable;
use ArtisanSdk\Contract\Taggable;
use ArtisanSdk\CQRS\Commands\Command;

/**
 * Place an Order Command.
 *
 * @param string $first_name
 * @param string $last_name
 * @param string $email
 * @param int    $total
 *
 * @return \App\Order\Entities\Entity
 */
class Place extends Command implements Eventable, Taggable
{
    /**
     * The tags for cache busting.
     *
     * @var string[]
     */
    public $tags = ['order'];

    /**
     * Prepare the event that fires before the command is executed.
     *
     * @param array $arguments
     *
     * @return string|\ArtisanSdk\Contract\Event
     */
    public function beforeEvent(array $arguments = [])
    {
        return new Placing($arguments);
    }

    /**
     * Run the command.
     *
     * @return \App\Order\Entities\Entity
     */
    public function run()
    {
        return new Entity($this->arguments());
    }

    /**
     * Prepare the event that fires after the command is executed.
     *
     * @param \App\Order\Entities\Entity $entity
     *
     * @return string|\ArtisanSdk\Contract\Event
     */
    public function afterEvent($entity)
    {
        return new Placed($entity);
    }
}
