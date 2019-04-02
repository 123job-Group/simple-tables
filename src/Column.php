<?php


namespace Bubooon\SimpleTables;


class Column
{

    private $config;

    private $attribute;
    private $value;
    private $label;
    private $style = '';

    private $isSort = false;
    private $filter = false;

    public function __construct($config)
    {
        $this->config = $config;
        $this->init();
    }

    public function hasFilter(): bool
    {
        return !!$this->filter;
    }

    public function getHeader(string $el = 'th'): string
    {

        $header = '<' . $el . ' style="' . $this->style . '">';
        if ($this->isSort) {
            $header .= $this->buildSortLink($this->label, $this->attribute);
        } else {
            $header .= $this->label;
        }

        $header .= '</' . $el . '>';
        return $header;
    }

    public function getValue($model): string
    {
        return '<td style="' . $this->style . '">' . call_user_func($this->value, $model) . '</td>';
    }

    public function getFilter(): string
    {
        $html = '<th style="' . $this->style . '">';
        $filter = request('filter');
        $val = $filter && isset($filter[$this->attribute]) ? $filter[$this->attribute] : '';

        if ($this->filter === true) {
            $html .= '<div class="simple-table-clearable"><span>×</span><input type="text" class="form-control form-control-sm" name="filter[' . $this->attribute . ']" value="' . $val . '" /></div>';
        } elseif (is_array($this->filter)) {
            $html .= '<select class="form-control form-control-sm" name="filter[' . $this->attribute . ']"/>';
            foreach ($this->filter as $k => $v) {
                $html .= '<option value="' . $k . '" ' . ((string)$k === $val ? 'selected' : '') . '>' . $v . '</option>';
            }
            $html .= '</select>';
        }
        $html .= '</th>';
        return $html;
    }

    private function buildSortLink($label, $key): string
    {
        $paramVal = request('sort');
        $linkClass = 'sorting';
        if ($paramVal == $key . '_asc') {
            $param = $key . '_desc';
            $linkClass = 'sorting-asc';
        } elseif ($paramVal == $key . '_desc') {
            $param = false;
            $linkClass = 'sorting-desc';
        } else {
            $param = $key . '_asc';
        }

        /* @var \Illuminate\Http\Request $r ; */
        $r = app('request');
        $params = $r->all();
        if ($param == false) {
            unset($params['sort']);
        } else {
            $params['sort'] = $param;
        }

        $url = '/' . $r->path() . '?' . http_build_query($params);

        return '<a href="' . $url . '" class="' . $linkClass . '">' . $label . '</a>';
    }


    private function init(): void
    {
        if (is_string($this->config)) {
            $filed_name = $this->config;
            $this->value = function ($model) use ($filed_name) {
                return $model->$filed_name;
            };
            $this->isSort = true;
            $this->label = $filed_name;
            $this->attribute = $filed_name;
        } elseif (is_array($this->config)) {
            $filed_name = $this->config['attribute'];
            $this->attribute = $filed_name;
            $this->value = $this->config['value'] ?? function ($model) use ($filed_name) {
                    return $model->$filed_name;
                };
            $this->label = $this->config['label'] ?? $filed_name;
            $this->isSort = $this->config['sort'] ?? true;
            $this->filter = $this->config['filter'] ?? false;
            $this->style = $this->config['style'] ?? '';
        }
    }
}