<?php


namespace Bubooon\SimpleTables\Providers;


interface IDataProvider
{
    public function getProvider();

    public function search();

    public function filter(string $field, string $key, string $operator = '='): void;

    public function fullSearch(array $fields): void;
}
