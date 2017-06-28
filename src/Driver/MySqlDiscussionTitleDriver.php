<?php
namespace GaNuongLaChanh\Search\Driver;

use Flarum\Core\Post;
use Flarum\Core\Discussion;
use Flarum\Core\Search\Discussion\Fulltext\DriverInterface;

class MySqlDiscussionTitleDriver implements DriverInterface
{
    /**
     * {@inheritdoc}
     */
    public function match($string)
    {
        $discussionIds = Discussion::whereRaw("is_approved = 1 AND title LIKE '%$string%'")
            ->orderBy('id', 'desc')
            ->lists('id','start_post_id');

        $relevantPostIds = [];

        foreach ($discussionIds as $postId => $discussionId) {
            $relevantPostIds[$discussionId][] = $postId;
        }


        $discussionIds = Post::where('type', 'comment')
            ->whereRaw('MATCH (`content`) AGAINST (? IN BOOLEAN MODE)', [$string])
            ->orderByRaw('MATCH (`content`) AGAINST (?) DESC', [$string])
            ->lists('discussion_id', 'id');

        foreach ($discussionIds as $postId => $discussionId) {
            $relevantPostIds[$discussionId][] = $postId;
        }

        return $relevantPostIds;
    }
}
