# Lravel Simple Tables

This is simple way to show data in Grids for user.

### Installing

Add composer package

```
composer require buboon/simple-tables
```

Register provider in config/app.php
```
Bubooon\SimpleTables\SimpleTableServiceProvider::class
```

Copy assets

```
php artisan vendor:publish --tag=simple-tables --force 
```

Add js to app.js

```
...
require('./simpletables');
...
```

Add css to app.sass

```
...
@import 'simple-tables';
```

Compile

```
npm run dev
```

Add code in layout or page for register JQuery plugin for table.
```
{!! $simpletable ?? '' !!}
```

### Usage (simplest)

```
$provider = new BuilderDataProvider((new User)->newQuery());
$grid = new SimpleTable(['id','email','created_at','updated_at']);
echo $grid->render();
```

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
