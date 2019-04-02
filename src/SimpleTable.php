<?php

namespace App\Services\SimpleTable;


use Illuminate\Pagination\LengthAwarePaginator;

class SimpleTable
{

    /* @var \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[] $data */
    private $data;
    private $columnsConfig;
    private $columns = [];
    /* @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
    private $paginator;
    private $hasFilters = false;
    private $id;
    private $options;
    private $fullSearch = false;
    private $pageSizes = [];
    private $showFooter = true;

    public function __construct($data, array $columnsConfig, array $options = [])
    {
        $this->columnsConfig = $columnsConfig;
        $this->options = $options;
        $this->data = $data;

        $this->init();
    }


    public function render(): string
    {
        $container = '<div id="' . $this->id . '">';
        $container .= $this->buidPreTable();
        $container .= $this->buildTable();
        $container .= $this->buildPagination();
        $container .= '</div>';

        $this->registerScripts();

        return $container;
    }

    private function buidPreTable(): string
    {
        $html = '<div class="simple-table-controls">';
        if (count($this->pageSizes)) {
            $html .= $this->buildPageSizes();
        }

        if($this->hasFilters){
            $html .= '<button class="btn btn-success btn-sm float-right simple-table-reset">Reset filters</button>';
        }

        if ($this->fullSearch) {
            $html .= $this->buildFullSearch();
        }

        $html .= '</div>';
        return $html;
    }

    private function buildFullSearch(): string
    {
        $fullSearch = request('fullSearch');
        return '<div class="form-inline float-right simple-table-clearable"><span>×</span>' .
            'Search:&nbsp;<input type="text" name="fullSearch" class="form-control form-control-sm" value="' . $fullSearch . '">' .
            '</div>';
    }

    private function buildPageSizes(): string
    {
        $pageSize = request('pageSize');
        if(!$pageSize){
            $pageSize = $this->paginator->perPage();
        }
        $html = '<div class="form-inline float-left">Show&nbsp;<select name="pageSize" class="form-control form-control-sm">';
        foreach ($this->pageSizes as $item) {
            $html .= '<option value="' . $item . '" ' . ($pageSize == $item ? 'selected' : '') . '>' . $item . '</option>';
        }
        $html .= '</select>&nbsp;entries</div>';
        return $html;
    }

    private function registerScripts(): void
    {
        //TODO need to search way without changes in layout
        app('view')->composer('layouts.app', function ($view) {
            return $view->with('simpletable', 'document.addEventListener(\'DOMContentLoaded\', function(){$(\'#' . $this->id . '\').simpletable();});');
        });
    }

    private function buildPagination(): string
    {
        $pagination = '';
        if ($this->paginator) {
            $pagination = $this->paginator
                ->appends([
                    'sort' => request('sort'),
                    'filter' => request('filter'),
                    'fullSearch' => request('fullSearch'),
                    'pageSize' => request('pageSize')
                ])
                ->links();
        }
        return $pagination;
    }

    private function buildTable(): string
    {
        $html = '<table class="table table-hover "><thead><tr>';

        foreach ($this->columns as $item) {
            $html .= $item->getHeader();
        }

        if ($this->hasFilters) {
            $html .= '</tr><tr class="simple-table-filters">';
            foreach ($this->columns as $item) {
                $html .= $item->getFilter();
            }
        }

        $html .= '</tr></thead><tbody>';
        foreach ($this->data as $item) {
            $html .= '<tr>';
            foreach ($this->columns as $column) {
                $html .= $column->getValue($item);
            }
            $html .= '</tr>';
        }

        if($this->showFooter){
            $html .= '</tbody><tfoot>';
            foreach ($this->columns as $item) {
                $html .= $item->getHeader('td');
            }
        }

        $html .= '</tfoot></table>';
        return $html;
    }

    private function init(): void
    {
        $this->id = 'st' . rand(0, 10000);
        $this->fullSearch = $this->options['fullSearch'] ?? false;
        $this->pageSizes = $this->options['pageSizes'] ?? [];
        $this->showFooter = $this->options['showFooter'] ?? true;
        if ($this->data instanceof LengthAwarePaginator) {
            $this->paginator = $this->data;
        }
        foreach ($this->columnsConfig as $column) {
            $c = new Column($column);
            $this->columns[] = $c;
            if ($c->hasFilter()) {
                $this->hasFilters = true;
            }
        }
    }
}