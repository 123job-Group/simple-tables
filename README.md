# Laravel Simple Tables

This is simple way to show data in grids for user.

**Screenshot:**

![screenshot](https://snag.gy/xQe2A5.jpg)

### Features
- Show tables with data using Illuminate\Database\Eloquent\Builder
- Set custom value for coumn using Closure
- Pagination
- Change page size
- Sort by column
- Filter by column (text input, dropdown)
- Create your own filters
- Full search input (search value from text input in list of columns)

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

Marckup of Simple Tables basad on Twitter Bootstrap 4, but you need include this styles 
by yourself, if you need it.

### Usage
Hello world

```php
$provider = new BuilderDataProvider((new User)->newQuery());
$grid = new SimpleTable($provieder->search(),['id','email','created_at','updated_at']);
echo $grid->render();
```

Setting pagination, default sort and fields for which sorting is available
```php
$provider = new BuilderDataProvider($query, [
    'pagination' => [
        'pageSize' => 25
    ],
    'fieldsList' => ['id', 'email', 'status', 'age', 'created_at'],
    'sort' => [
        'id' => 'DESC'
    ]
]);
```
Add filters for columns
```php
$provider->filter('email', 'email', 'like'); // serach substring
$provider->filter('was_found', 'was_found'); // strict search
$provider->filter('transaction_id', 'transaction_id', 'is_null'); //0 - IS NULL, 1 - IS NOT NULL, null - nothing
```
Setting fields for full search
```php
$fields = ['email', 'description', 'first_name', 'last_name'];
$provider->fullSearch($fields);
```

Add your custom filtering
```php
if ($last_update = request('date_created')) {
    switch ($last_update) {
        case 1:
            $provider->whereRaw('str_to_date(result.response ->> "$**.LastUpdatedDate", \'["%d/%m/%Y %T"]\') > now() - INTERVAL 1 WEEK');
            break;
        case 2:
            $provider->whereRaw('str_to_date(result.response ->> "$**.LastUpdatedDate", \'["%d/%m/%Y %T"]\') > now() - INTERVAL 1 MONTH');
            break;
    }
}
```

Setting fields in table
```php
$table = new SimpleTable($provider->serch(), [
    'id', //just show value with defautl sorts by this column
    [
        'attribute' => 'email',
        'sort' => false, //remove ability to sort by this column
        'filter' => true, //add input[type=text] filter for this column 
        'label' => 'User email', //set custom header of column,
        'style' => 'width:250px' //css style for column
    ],
    [
        'attribute' => 'status',
        'filter' => [ //add dropdown filter
            '' => '',
            1 => 'Available',
            0 => 'Not available'
        ],
        'value' => function($row){
            return $row->status ? 'available' : 'not available';
        }
    ]
], [
    'fullsearch' => true, //add full search field
    'pageSizes' => [10, 25, 50, 100], //set available sizes of page,
    'showFooter' => false //hide table footer
]);
```


### Features planned
- ArrayDataProvider
- Online demo with examples
- Support of multiple tables on one page
- More customization
- Write beautiful code instead of ugliness

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
