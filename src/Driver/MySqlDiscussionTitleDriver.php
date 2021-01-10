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
        $discussionIds = Discussion::where("is_approved", 1)
            ->where("is_private", 0)
            ->whereNull('hidden_at')
            ->where('comment_count', '>', 0)
            ->where('title', 'like', '%' . $string . '%')
            ->limit(20)
            ->pluck('id','first_post_id');

        $relevantPostIds = [];

        foreach ($discussionIds as $postId => $discussionId) {
            $relevantPostIds[$discussionId][] = $postId;
        }


        $discussionIds = Post::where('type','=', 'comment')
            ->where("is_approved", 1)
            ->where("is_private", 0)
            ->whereNull('hidden_at')
            ->whereRaw('MATCH (`content`) AGAINST (? IN NATURAL LANGUAGE MODE)', [$string])
            //->orderByRaw('MATCH (`content`) AGAINST (?) DESC', [$string])
            ->limit(20)
            ->pluck('discussion_id', 'id');

        foreach ($discussionIds as $postId => $discussionId) {
            $relevantPostIds[$discussionId][] = $postId;
        }

        return $relevantPostIds;
    }
}
