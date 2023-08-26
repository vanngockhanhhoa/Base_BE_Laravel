<?php

namespace App\Http\Parameters;

use Helper\Common;
use Illuminate\Http\Request;

/**
 * Class Criteria
 * @package App\Http\Parameters
 */
class Criteria
{
    /**
     * @var array
     */
    private array $select = [];

    /**
     * @var array
     */
    private array $filters = [];

    /**
     * @var array
     */
    private array $sorts = [];

    /**
     * @var array
     */
    private array $relations = [];

    /**
     * @var array
     */
    private array $countRelations = [];

    /**
     * @var int
     */
    private int $page = 1;

    /**
     * @var int|null
     */
    private ?int $limit;

    /**
     * @param Request $request
     * @return Criteria
     */
    public static function createFromRequest(Request $request): Criteria
    {
        $filters = $request->get('filter', []);
        $extraFilters = $request->only(['search']);
        return (new static())->setFilters(array_merge($filters, $extraFilters))
            ->setSorts($request->get('sort', []))
            ->setSelect($request->get('select', []))
            ->setPagination(
                $request->get(config('pagination.page_name')) ?
                    (int) $request->get(config('pagination.page_name')) : 1,
                $request->get(config('pagination.per_page_name')) ?
                    (int) $request->get(config('pagination.per_page_name')) : null
            );
    }

    /**
     * Set pagination param: item per page and current page
     * @param  int  $page
     * @param ?int  $limit
     * @return Criteria
     */
    public function setPagination(int $page, ?int $limit): Criteria
    {
        $this->page = $page;
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get array filter conditions.
     * @return array
     */
    public function getFilters(): array
    {
        foreach ($this->filters as $filterField => $filterValue) {
            $this->filters[$filterField] = $filterValue;
        }
        return $this->filters;
    }

    /**
     * Get array filter conditions for dropdown
     * @return array
     */
    public function getFiltersDropdown(): array
    {
        foreach ($this->filters as $filterField => $filterValue) {
            $this->filters[$filterField] = $filterValue;
        }
        return $this->filters;
    }

    /**
     * Set filter conditions
     * @param  array  $filters
     * @return Criteria
     */
    public function setFilters(array $filters): Criteria
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Add condition to filters
     * @param  string|array  $field
     * @param  int|string|null  $value
     * @return Criteria
     */
    public function addFilter($field, $value = null): Criteria
    {
        if (is_array($field)) {
            foreach ($field as $filterField => $filterValue) {
                $this->filters[$filterField] = $filterValue;
            }

            return $this;
        }

        $this->filters[$field] = $value;

        return $this;
    }

    /**
     * Get filter condition by key name
     * @param  string  $field
     * @return mixed|null
     */
    public function getFilter($field)
    {
        return $this->filters[$field] ?? null;
    }

    /**
     * Remove filter
     * @param $field
     */
    public function removeFilter($field)
    {
        if (isset($this->filters[$field])) {
            unset($this->filters[$field]);
        }
    }

    /**
     * Get sort conditions
     * @return array
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * Get sort conditions
     * @param  array  $sorts
     * @return Criteria
     */
    public function setSorts(array $sorts): Criteria
    {
        $this->sorts = $sorts;

        return $this;
    }

    /**
     * Get current page
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Get limit item per page
     * @return int
     */
    public function getLimit(): int
    {
        $limit = $this->limit ?? config('pagination.per_page_number');
        $maxLimit = config('pagination.max_per_page_number');
        return $limit > $maxLimit ? $maxLimit : $limit;
    }

    /**
     * Get select columns
     * @return array
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * Set select columns
     * @param  array  $select
     * @return $this
     */
    public function setSelect(array $select): Criteria
    {
        $this->select = $select;
        return $this;
    }

    /**
     * Get relationships
     * @return array
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * Set relationships
     * @param  array  $relations
     * @return Criteria
     */
    public function setRelations(array $relations): Criteria
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * Get count relationships
     * @return array
     */
    public function getCountRelations(): array
    {
        return $this->countRelations;
    }

    /**
     * Set count relationships
     * @param  array  $relations
     * @return Criteria
     */
    public function setCountRelations(array $relations): Criteria
    {
        $this->countRelations = $relations;

        return $this;
    }
}
