<?php namespace GaNuongLaChanh\Sonic;

use Flarum\Extend;
use Illuminate\Events\Dispatcher;

return [
    (new Extend\ServiceProvider())
        ->register(Providers\SearchServiceProvider::class)
    ,
    function(Dispatcher $events) {        
        $events->subscribe(Listeners\AddClientGamBit::class);
    }
];