<?php

namespace Bubooon\SimpleTables;


use Illuminate\Support\ServiceProvider;

class SimpleTableServiceProvider extends ServiceProvider
{

    public function boot()
    {

        $this->publishes([
            __DIR__ . '/assets/js' => resource_path('js'),
            __DIR__ . '/assets/sass' => resource_path('sass'),
            __DIR__ . '/assets/images' => public_path('images')
        ], 'simple-tables');
    }

}
