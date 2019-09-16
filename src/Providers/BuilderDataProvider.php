<?php


namespace Bubooon\SimpleTables\Providers;


use Illuminate\Database\Eloquent\Builder;

class BuilderDataProvider implements IDataProvider
{
    private $provider;
    private $paginationConfig = false;
    private $fieldsList = [];
    private $sort = [];

    public function __construct(Builder $provider, array $config = [])
    {
        $this->provider = $provider;
        $this->loadConfig($config);
    }

    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Paginate the given query.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
     */
    public function search()
    {
        $this->sort();

        if ($this->paginationConfig) {
            return $this->paginate();
        } else {
            return $this->provider->get();
        }
    }

    public function filter(string $field, string $key, string $operator = '='): void
    {
        $filter = request('filter');
        if ($filter && isset($filter[$key]) && strlen($filter[$key])) {
            $val = $filter[$key];
            switch ($operator) {
                case '=':
                    $this->provider->where($field, $val);
                    break;
                case 'like' :
                    $this->provider->where($field, 'like', "%$val%");
                    break;
                case 'is_null':
                    if ($val == 0) {
                        $this->provider->whereNull($field);
                    } else {
                        $this->provider->whereNotNull($field);
                    }

                    break;
            }

        }
    }

    public function fullSearch(array $fields): void
    {
        $fullSearch = request('fullSearch');
        if ($fullSearch) {
            $where = array_map(function ($item) use ($fullSearch) {
                return $item . ' LIKE "%' . $fullSearch . '%"';
            }, $fields);
            $this->provider->whereRaw(implode(' OR ', $where));
        }

    }

    private function loadConfig(array $config): void
    {
        $this->paginationConfig = $config['pagination'] ?? false;
        $this->fieldsList = $config['fieldsList'] ?? [];
        $this->sort = $config['sort'] ?? [];
    }

    private function paginate()
    {
        $pageSize = request('pageSize');
        if (!$pageSize) {
            $pageSize = $this->paginationConfig['pageSize'];
        }
        return $this->provider->paginate($pageSize);
    }

    private function sort(): void
    {
        $sort = request('sort');
        if ($sort) {
            if (substr($sort, strlen($sort) - 5) == '_desc') {
                $sort = substr($sort, 0, strlen($sort) - 5);
                $order = 'DESC';
            } else {
                $sort = substr($sort, 0, strlen($sort) - 4);
                $order = 'ASC';
            }

            if (in_array($sort, $this->fieldsList)) {
                $this->provider->orderBy($sort, $order);
            }
        } elseif (count($this->sort)) {
            foreach ($this->sort as $k => $v) {
                $this->provider->orderBy($k, $v);
            }
        }
    }
}
