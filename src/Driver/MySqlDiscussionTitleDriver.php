<?php
namespace GaNuongLaChanh\Sonic\Driver;

use Flarum\Post\Post;
use Flarum\Discussion\Discussion;

class MySqlDiscussionTitleDriver
{
    /**
     * {@inheritdoc}
     */
    public function match($string)
    {
        //echo $string .PHP_EOL;
        $search = new \Psonic\Search(new \Psonic\Client('sonic', 1491, 30));
        $search->connect('SecretPassword');
        $res = $search->query('postCollection', 'flarumBucket', $string, 20, 0, 'vie');
        // you should be getting an array of object keys which matched with the term $string
        $search->disconnect();
        $relevantPostIds = [];
        if (is_array($res)) {
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
        //return $relevantPostIds;
        //exit(0);
        $discussionIds = Discussion::where("is_approved", 1)
            ->where("is_private", 0)
            ->whereNull('hidden_at')
            ->where('comment_count', '>', 0)
            ->where('title', 'like', '%' . $string . '%')
            ->limit(20)
            ->pluck('id','first_post_id');

        //var_dump($discussionIds);

        foreach ($discussionIds as $postId => $discussionId) {
            //echo $postId .PHP_EOL;
            //echo $discussionId .PHP_EOL;
            $relevantPostIds[$discussionId][] = $postId;
        }
        return $relevantPostIds;
        /*$relevantPostIds = [];
        $discussionIds = Post::where('type','=', 'comment')
            ->where("is_approved", 1)
            ->where("is_private", 0)
            ->whereNull('hidden_at')
            ->whereRaw('MATCH (`content`) AGAINST (? IN NATURAL LANGUAGE MODE)', [$string])
            //->orderByRaw('MATCH (`content`) AGAINST (?) DESC', [$string])
            ->limit(20)
            ->pluck('discussion_id', 'id');
        //var_dump($discussionIds);
        foreach ($discussionIds as $postId => $discussionId) {
            //echo $postId .PHP_EOL;
            //echo $discussionId .PHP_EOL;
            $relevantPostIds[$discussionId][] = $postId;
        }

        return $relevantPostIds;*/
        
    }
}
