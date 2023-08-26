<?php

namespace App\Http\Parameters;

use Illuminate\Http\Request;

class Relation
{
    /** @var array $eagerLoad */
    private $eagerLoad;

    /**
     * Relation constructor.
     *
     * @param  array  $relations
     */
    public function __construct(array $relations)
    {
        $this->eagerLoad = $relations;
    }

    /**
     * Create criteria from current request
     *
     * @param  Request  $request
     *
     * @return Relation
     */
    public static function createFromRequest(Request $request): Relation
    {
        return (new static($request->get('load', [])));
    }

    /**
     * Get list eager load
     *
     * @return array
     */
    public function list(): array
    {
        return $this->eagerLoad;
    }

    /**
     * Add relations
     *
     * @param array|string $relations
     */
    public function add($relations): void
    {
        if (is_string($relations)) {
            $relations = [$relations];
        }
        $this->eagerLoad = array_merge($this->eagerLoad, $relations);
    }
}
