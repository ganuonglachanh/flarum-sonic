<?php 
namespace GaNuongLaChanh\Sonic\Console;

use Flarum\Console\AbstractCommand;
use Illuminate\Contracts\Container\Container;
use Flarum\Post\Post;
use Flarum\Settings\SettingsRepositoryInterface;
use Psonic;

class AddToIndex extends AbstractCommand
{
    protected $container;
    protected $database;

    public function __construct(Container $container, SettingsRepositoryInterface $settings)
    {
        parent::__construct();
        $this->container = $container;
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sonic:addtoindex')
            ->setDescription('Add data to sonic index');
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        $this->info('Starting...');

        $locale = $this->settings->get('ganuonglachanh-sonic.locale', 'eng');
        $locale = $locale === '' ? 'eng' : $locale;
        $password = $this->settings->get('ganuonglachanh-sonic.password', 'SecretPassword');
        $password = $password === '' ? 'SecretPassword' : $password;
        $host = $this->settings->get('ganuonglachanh-sonic.host', '127.0.0.1');
        $host = $host === '' ? '127.0.0.1' : $host;
        $port = intval($this->settings->get('ganuonglachanh-sonic.port', 1491));
        $port = $port === 0 ? 1491 : $port;
        $timeout = intval($this->settings->get('ganuonglachanh-sonic.timeout', 30));
        $timeout = $timeout === 0 ? 30 : $timeout;
        // https://github.com/ppshobi/psonic/blob/master/api-docs.md
        $ingest = new Ingest(new Client($host, $port, $timeout));
        $control = new Control(new Client($host, $port, $timeout));
        try {
            $ingest->connect($password);
            $control->connect($password);
        } catch (\Throwable $e) {
            $this->error('Invalid sonic server detail!');
            return;
        }
        
        $this->info('Flush old postCollection: ' . $ingest->flushc('postCollection'));
        $this->info('Adding to index...');
        $this->withProgressBar(
            Post::select('id', 'content')
                ->where('type', '=', 'comment')
                ->where('is_approved', 1)
                ->where('is_private', 0)
                ->whereNull('hidden_at'),
            function ($post) use ($ingest, $locale) {
                try {
                    $ingest->push('postCollection', 'flarumBucket', $post->id, strip_tags($post->content), $locale);
                } catch (\Throwable $e) {
                    $this->error("{$post->id} with " . strip_tags($post->content) . ' bytes of content failed after' . round((microtime(true) - $start) * 1000, 2) . 'ms' . PHP_EOL);
                }
            }
        );
        $this->info($control->consolidate()); // saves the data to disk
        $ingest->disconnect();
        $control->disconnect();
        $this->info('Done!');
    }
}
