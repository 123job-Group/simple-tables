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
```php
{!! $simpletable ?? '' !!}
```

### Usage (simplest)

```php
$provider = new BuilderDataProvider((new User)->newQuery());
$grid = new SimpleTable(['id','email','created_at','updated_at']);
echo $grid->render();
```

### Future planes
- Add ArrayDataProvider
- Add screenshots and online demo with examples
- Add support of multiple tables on one page
- Add more customization

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
