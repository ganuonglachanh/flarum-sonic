<?php
namespace GaNuongLaChanh\Sonic\Event;

use Flarum\Post\Event\Hidden;
use Flarum\Post\Event\Deleted;
use Flarum\Post\Event\Posted;
use Flarum\Post\Event\Restored;
use Flarum\Post\Event\Revised;
use Illuminate\Contracts\Events\Dispatcher;
use Flarum\Settings\SettingsRepositoryInterface;

class SonicEventSubscriber
{
    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
        $locale = $this->settings->get('ganuonglachanh-sonic.locale','eng');
        $this->locale = $locale === '' ? 'eng' : $locale;
        $password = $this->settings->get('ganuonglachanh-sonic.password','SecretPassword');
        $password = $password === '' ? 'SecretPassword' : $password;
        $host = $this->settings->get('ganuonglachanh-sonic.host','127.0.0.1');
        $host = $host === '' ? '127.0.0.1' : $host;
        $port = intval($this->settings->get('ganuonglachanh-sonic.port',1491));
        $port = $port === 0 ? 1491 : $port;
        $timeout = intval($this->settings->get('ganuonglachanh-sonic.timeout',30));
        $timeout = $timeout === 0 ? 30 : $timeout;
        $this->ingest  = new \Psonic\Ingest(new \Psonic\Client($host, $port, $timeout));
        $this->control = new \Psonic\Control(new \Psonic\Client($host, $port, $timeout));
        $this->ingest->connect($password);
        $this->control->connect($password);
    }

    function __destruct() {
        $this->control->consolidate(); // saves the data to disk
        $this->ingest->disconnect();
        $this->control->disconnect();
    }

    /**
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(Posted::class, [$this, "posted"]);
        $events->listen(Revised::class, [$this, "revised"]);
        $events->listen(Hidden::class, [$this, "hidden"]);
        $events->listen(Deleted::class, [$this, "deleted"]);
        $events->listen(Restored::class, [$this, "restored"]);
    }

    function posted(Posted $event)
    {
        if ($event->post->type === "comment") {
            $this->ingest->push('postCollection', 'flarumBucket', $event->post->id,$event->post->content, $this->locale);
        }
    }

    function revised(Revised $event)
    {
        if ($event->post->type === "comment") {
            //$this->ingest->pop('postCollection', 'flarumBucket', $event->post->id,$event->post->content);
            $this->ingest->push('postCollection', 'flarumBucket', $event->post->id,$event->post->content, $this->locale);
        }
    }

    function hidden(Hidden $event) {
        if ($event->post->type === "comment") {
            $this->ingest->pop('postCollection', 'flarumBucket', $event->post->id,$event->post->content);
        }
    }

    function deleted(Deleted $event) {
        if ($event->post->type === "comment") {
            $this->ingest->pop('postCollection', 'flarumBucket', $event->post->id,$event->post->content);
        }
    }

    function restored(Restored $event) {
        if ($event->post->type === "comment") {
            $this->ingest->push('postCollection', 'flarumBucket', $event->post->id,$event->post->content, $this->locale);
        }
    }
}