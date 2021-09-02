<?php namespace GaNuongLaChanh\Sonic;

use Flarum\Extend;
use GaNuongLaChanh\Sonic\Gambit\TitleGambit;
use Flarum\Discussion\Search\DiscussionSearcher;
use GaNuongLaChanh\Sonic\Event\SonicEventSubscriber;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),
    (new Extend\Locales(__DIR__ . '/resources/locale')),
    (new Extend\SimpleFlarumSearch(DiscussionSearcher::class))
        ->setFullTextGambit(TitleGambit::class),
    (new Extend\Console())->command(\GaNuongLaChanh\Sonic\Console\AddToIndex::class),
    (new Extend\Event)
        ->subscribe(SonicEventSubscriber::class),
];