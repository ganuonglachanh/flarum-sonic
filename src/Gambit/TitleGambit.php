<?php
namespace GaNuongLaChanh\Sonic\Gambit;

use Flarum\Search\SearchState;
use GaNuongLaChanh\Sonic\Driver\MySqlDiscussionTitleDriver;
use Flarum\Search\GambitInterface;
use Flarum\Post\Post;
use Illuminate\Database\Query\Expression;

class TitleGambit implements GambitInterface
{
    /**
     * @var MySqlDiscussionTitleDriver
     */
    protected $titleGambit;

    /**
     * @param MySqlDiscussionTitleDriver $titleGambit
     */
    public function __construct(MySqlDiscussionTitleDriver $titleGambit)
    {
        $this->titleGambit = $titleGambit;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(SearchState $search, $bit)
    {
        // Replace all non-word characters with spaces.
        // We do this to prevent MySQL fulltext search boolean mode from taking
        // effect: https://dev.mysql.com/doc/refman/5.7/en/fulltext-boolean.html
        $bit = preg_replace('/[^\p{L}\p{N}_]+/u', ' ', $bit);

        if (! isset($bit) || strlen($bit)<=3) return $search;

        $query = $search->getQuery();
        $grammar = $query->getGrammar();

        $relevantPostIds = $this->titleGambit->match($bit);
        $discussionIds = array_keys($relevantPostIds);
        $postIdsArr = array_values($relevantPostIds);
        $postIds = array();
        array_walk_recursive($postIdsArr,function($v) use (&$postIds){ $postIds[] = $v; }); // flatten array
        $subquery = Post::whereVisibleTo($search->getActor())
            ->select(['id as most_relevant_post_id','discussion_id'])
            ->whereIn('posts.id', $postIds);

        $query
            ->addSelect('posts_ft.most_relevant_post_id')
            ->join(
                new Expression('(' . $subquery->toSql() . ') ' . $grammar->wrapTable('posts_ft')),
                'posts_ft.discussion_id',
                '=',
                'discussions.id'
            )
            ->groupBy('discussions.id')
            ->addBinding($subquery->getBindings(), 'join');


        $search->getQuery()->whereIn('discussions.id', $discussionIds);
        //$search->setDefaultSort(['id' => $discussionIds]);
        $search->setDefaultSort(['discussions.id' => 'desc']);
    }
}
