<?php namespace GaNuongLaChanh\Sonic;

use Flarum\Extend;
use Illuminate\Events\Dispatcher;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),
    (new Extend\Locales(__DIR__ . '/resources/locale')),
    (new Extend\ServiceProvider())
        ->register(Providers\SearchServiceProvider::class)
    ,    
    (new Extend\Console())->command(\GaNuongLaChanh\Sonic\Console\AddToIndex::class),
    function(Dispatcher $events) {        
        $events->subscribe(Listeners\AddClientGamBit::class);
    }
];