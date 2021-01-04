<?php
namespace GaNuongLaChanh\Search\Gambit;

use Flarum\Search\AbstractSearch;
use Flarum\Discussion\Search\DiscussionSearch;
use GaNuongLaChanh\Search\Driver\MySqlDiscussionTitleDriver;
use Flarum\Search\GambitInterface;
use LogicException;

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
    public function apply(AbstractSearch $search, $bit)
    {
        if (! $search instanceof DiscussionSearch) {
            throw new LogicException('This gambit can only be applied on a DiscussionSearch');
        }
        
        // Replace all non-word characters with spaces.
        // We do this to prevent MySQL fulltext search boolean mode from taking
        // effect: https://dev.mysql.com/doc/refman/5.7/en/fulltext-boolean.html
        $bit = preg_replace('/[^\p{L}\p{N}_]+/u', ' ', $bit);

        if (! isset($bit) || strlen($bit)<=3) return $search;

        $relevantPostIds = $this->titleGambit->match($bit);
        $discussionIds = array_keys($relevantPostIds);
        $old_relevantPostIds = $search->getRelevantPostIds();
        $relevantPostIds = array_merge($relevantPostIds, $old_relevantPostIds);
        $search->setRelevantPostIds($relevantPostIds);
        $search->getQuery()->whereIn('id', $discussionIds);
        $search->setDefaultSort(['id' => $discussionIds]);
    }
}
