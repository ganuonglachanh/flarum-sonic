<?php
namespace GaNuongLaChanh\Search\Listeners;

use DirectoryIterator;
use Flarum\Event\ConfigureDiscussionGambits;
use Illuminate\Contracts\Events\Dispatcher;

class AddClientAssets
{

    public function subscribe(Dispatcher $events)
    {
        $events->listen(ConfigureDiscussionGambits::class, [$this, 'addAssets']);

    }

    public function addAssets(ConfigureDiscussionGambits $event)
    {
        $event->gambits->setFulltextGambit('GaNuongLaChanh\Search\Gambit\TitleGambit');
    }


}
