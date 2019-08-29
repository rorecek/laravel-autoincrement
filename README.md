# Laravel AutoIncrement

This package will help you set table AUTO_INCREMENT values in your Laravel application.

### Why do you need to change auto-increment values?

If you do not change auto-increment values of your tables you are exposing
how many records you have in there. Imagine some user makes an order and
is redirected to this order detail URL `https://example.com/orders/10`.
User will know then that there are only 10 orders in the whole system which
is probably information you do not want to share. If you will change your
table to start with a higher auto-increment number, for example, 512322,
it will not be that obvious (`https://example.com/orders/512332`).

But what if he makes another order month from now? There will be only small
difference between these numbers and anyone can easily calculate your monthly
activity. There is an easy solution for this. Add auto-increment update
toLaravel's command scheduler and add some random number to your next auto-increment id
automatically on hourly, daily, monthly, etc., basis.

### What are the benefits?

* Integer values are way more efficient than UUIDs or other types of string ids 

* Sorting by id still leads to chronological order

* You do not expose the total number of resources in your database, for example `https://example.com/users/10`

## Installation

You can install this package via composer using this command:

``` bash
 composer require rorecek/laravel-autoincrement
```

## Usage

You can use this package to set the next auto-increment id to a specific value,
or to add a number to current value or to reset auto-increment to the lowest
possible value.

``` php
AutoIncrement::table('items')->set(100);
// Set table auto-increment value to 100

AutoIncrement::table('items')->add(500);
// Increase current auto-increment value by 500

AutoIncrement::table('items')->addRandomBetween(10, 100);
// Increase current auto-increment value by random number between 10 and 100

AutoIncrement::table('items')->reset();
// Reset auto-increment to the lowest value possible taking existing records in consideration.
```

### Migrations

Set auto-increments directly in your migrations.

``` php
Schema::create('items', function (Blueprint $table) {
  $table->bigIncrements('id');
  $table->timestamps();
});

AutoIncrement::table('items')->set(101);
// Next auto-increment id will be 101
```

### Scheduler

Setup automatic auto-increment increases using task scheduler.

``` php
// App\Console\Kernel.php

protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        AutoIncrement::table('messages')->add(35);
    })->hourly();

    $schedule->call(function () {
        AutoIncrement::table('registrations')->addRandomBetween(10, 100);
        AutoIncrement::table('bookings')->addRandomBetween(100, 500);
    })->daily();
}
```

## Advanced usage

You can optionally use different connections and/or closures if needed.

``` php
AutoIncrement::connection('foo')->table('items')->...
// Using different connection

AutoIncrement::table('items')->set(function () {
    return (int) date('ymd') . 1001;
});
// Using closure
```

## Support

If you believe you have found an issue, please report it using the [GitHub issue tracker](https://github.com/rorecek/laravel-ulid/issues), or better yet, fork the repository and submit a pull request.

If you're using this package, I'd love to hear your thoughts. Thanks!

## License

The MIT License (MIT). [Pavel Rorecek](https://laravelist.com)
