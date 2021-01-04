<?php
namespace GaNuongLaChanh\Search\Listeners;

use Flarum\Event\ConfigureDiscussionGambits;
use Illuminate\Contracts\Events\Dispatcher;
use GaNuongLaChanh\Search\Gambit\TitleGambit;

class AddClientGamBit
{

    public function subscribe(Dispatcher $events)
    {
        $events->listen(ConfigureDiscussionGambits::class, [$this, 'addGamBit']);

    }

    public function addGamBit(ConfigureDiscussionGambits $event)
    {
        $event->gambits->setFulltextGambit(TitleGambit::class);
    }


}
