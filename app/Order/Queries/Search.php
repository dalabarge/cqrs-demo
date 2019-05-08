<?php

namespace App\Order\Queries;

use App\Order\Entities\Entity;
use ArtisanSdk\Contract\Cacheable;
use ArtisanSdk\CQRS\Queries\Query;
use BadMethodCallException;

/**
 * Search Orders Query.
 *
 * @param string $keyword
 *
 * @return \Illuminate\Support\Collection[\App\Order\Entities\Entity]
 */
class Search extends Query implements Cacheable
{
    /**
     * The TTL for the cache in seconds.
     *
     * @var int
     */
    public $ttl = 3600;

    /**
     * The tags for cache busting.
     *
     * @var string[]
     */
    public $tags = ['order'];

    /**
     * Run the command.
     *
     * @return \Illuminate\Support\Collection[\App\Order\Entities\Entity]
     */
    public function run()
    {
        $entity = new Entity([
            'email'      => 'johndoe@example.com',
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'total'      => 10000,
        ]);

        $orders = collect([$entity]);

        if ($this->hasOption('keyword')) {
            $orders = $orders->where('email', '=', $this->argument('keyword', 'is_string'));
        }

        return $orders;
    }

    /**
     * {@inheritdoc}
     */
    public function builder()
    {
        throw new BadMethodCallException('Method '.__METHOD__.'() not callable.');
    }
}
