<?php
namespace GaNuongLaChanh\Sonic\Gambit;

use Flarum\Search\SearchState;
use GaNuongLaChanh\Sonic\Driver\MySqlDiscussionTitleDriver;
use Flarum\Search\GambitInterface;

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

        $relevantPostIds = $this->titleGambit->match($bit);
        $discussionIds = array_keys($relevantPostIds);
        $search->getQuery()->whereIn('id', $discussionIds);
        $search->setDefaultSort(['id' => $discussionIds]);
    }
}