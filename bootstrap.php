<?php namespace GaNuongLaChanh\Search;

use Illuminate\Contracts\Events\Dispatcher;
use Flarum\Foundation\Application;

return function(Dispatcher $events, Application $app) {
    $app->register(Providers\SearchServiceProvider::class);
    $events->subscribe(Listeners\AddClientAssets::class);
};
