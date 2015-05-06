<?php

namespace OpenOrchestra\ModelBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use Solution\MongoAggregation\Pipeline\Stage;
use Solution\MongoAggregationBundle\AggregateQuery\AggregationQueryBuilder;

/**
 * Class AbstractRepository
 */
abstract class AbstractRepository extends DocumentRepository
{
    /**
     * @var CurrentSiteIdInterface
     */
    protected $currentSiteManager;

    /**
     * @var AggregationQueryBuilder
     */
    private $aggregationQueryBuilder;

    /**
     * @param CurrentSiteIdInterface $currentSiteManager
     */
    public function setCurrentSiteManager(CurrentSiteIdInterface $currentSiteManager)
    {
        $this->currentSiteManager = $currentSiteManager;
    }

    /**
     * @param AggregationQueryBuilder $aggregationQueryBuilder
     */
    public function setAggregationQueryBuilder($aggregationQueryBuilder)
    {
        $this->aggregationQueryBuilder = $aggregationQueryBuilder;
    }

    /**
     * @param string|null $stage
     *
     * @return Stage
     */
    protected function createAggregationQuery($stage = null)
    {
        return $this->aggregationQueryBuilder->getCollection($this->getClassName())->createAggregateQuery($stage);
    }

    /**
     * @param Stage  $qa
     * @param string $elementName
     *
     * @return array
     */
    protected function hydrateAggregateQuery(Stage $qa, $elementName)
    {
        $contents = $qa->getQuery()->aggregate();

        $contentCollection = array();

        foreach ($contents as $content) {
            $contentCollection[] = $this->getDocumentManager()->getUnitOfWork()->getOrCreateDocument($this->getClassName(), $content[$elementName]);
        }

        return $contentCollection;
    }
}