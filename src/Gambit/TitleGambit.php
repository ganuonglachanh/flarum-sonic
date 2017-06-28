<?php
namespace GaNuongLaChanh\Search\Gambit;

use Flarum\Core\Search\AbstractSearch;
use Flarum\Core\Search\Discussion\DiscussionSearch;
use GaNuongLaChanh\Search\Driver\MySqlDiscussionTitleDriver;
use Flarum\Core\Search\GambitInterface;
use LogicException;

class TitleGambit implements GambitInterface
{
    /**
     * @var MySqlDiscussionTitleDriver
     */
    protected $titletext;

    /**
     * @param MySqlDiscussionTitleDriver $titletext
     */
    public function __construct(MySqlDiscussionTitleDriver $titletext)
    {
        $this->titletext = $titletext;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(AbstractSearch $search, $bit)
    {
        if (! $search instanceof DiscussionSearch) {
            throw new LogicException('This gambit can only be applied on a DiscussionSearch');
        }

        if (! isset($bit) || strlen($bit)<=3) return $search;

        $relevantPostIds = $this->titletext->match($bit);
        $discussionIds = array_keys($relevantPostIds);
        $old_relevantPostIds = $search->getRelevantPostIds();
        $relevantPostIds = array_merge($relevantPostIds, $old_relevantPostIds);
        $search->setRelevantPostIds($relevantPostIds);
        $search->getQuery()->whereIn('id', $discussionIds);
        $search->setDefaultSort(['id' => $discussionIds]);
    }
}
