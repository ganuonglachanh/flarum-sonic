<?php
namespace GaNuongLaChanh\Sonic\Driver;

use Flarum\Post\Post;
use Flarum\Discussion\Discussion;
use Illuminate\Contracts\Container\Container;
use Flarum\Settings\SettingsRepositoryInterface;
class MySqlDiscussionTitleDriver
{
    public function __construct(Container $container, SettingsRepositoryInterface $settings)
    {
        $this->container = $container;
        $this->settings = $settings;
    }
    /**
     * {@inheritdoc}
     */
    public function match($string)
    {
        $relevantPostIds = [];
        // 1) Search in discussion title first
        $discussionIds = Discussion::where("is_approved", 1)
            ->where("is_private", 0)
            ->whereNull('hidden_at')
            ->where('comment_count', '>', 0)
            ->where('title', 'like', '%' . $string . '%')
            ->limit(20)
            ->pluck('id','first_post_id');

        foreach ($discussionIds as $postId => $discussionId) {
            $relevantPostIds[$discussionId][] = $postId;
        }
        
        // 2) Then serch in post body by sonic
        $locale = $this->settings->get('ganuonglachanh-sonic.locale','eng');
        $password = $this->settings->get('ganuonglachanh-sonic.password','SecretPassword');
        $host = $this->settings->get('ganuonglachanh-sonic.host','127.0.0.1');
        $port = intval($this->settings->get('ganuonglachanh-sonic.port',1491));
        $timeout = intval($this->settings->get('ganuonglachanh-sonic.timeout',30));
        //echo $string .PHP_EOL;
        $search = new \Psonic\Search(new \Psonic\Client($host, $port, $timeout));
        $search->connect($password);
        $res = $search->query('postCollection', 'flarumBucket', $string, 20, 0, $locale);
        // you should be getting an array of object keys which matched with the term $string
        $search->disconnect();
        
        if (is_array($res) && count($res) > 0) {
            //var_dump($res);
            //$discussionIds = Post::select('id','discussion_id')
            $discussionIds = Post::where('type','=', 'comment')
            ->where('is_approved', 1)
            ->where('is_private', 0)
            ->whereNull('hidden_at')
            ->whereIn('id', $res)
            ->limit(20)
            ->pluck('discussion_id', 'id');
            foreach ($discussionIds as $postId => $discussionId) {
                //echo $postId .PHP_EOL;
                //echo $discussionId .PHP_EOL;
                $relevantPostIds[$discussionId][] = $postId;
            }
            
        }

        return $relevantPostIds;
    }
}
