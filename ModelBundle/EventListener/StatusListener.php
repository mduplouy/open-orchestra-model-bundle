<?php

namespace PHPOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\PostFlushEventArgs;
use PHPOrchestra\ModelBundle\Document\Status;
use PHPOrchestra\ModelBundle\Repository\StatusRepository;
/**
 * Class StatusListener
 */
class StatusListener 
{

    protected $statuses = [];

    /**
     * @param LifecycleEventArgs $eventArgs
     *
     */
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        $documentManager = $eventArgs->getDocumentManager();
        if($document instanceof Status && $document->isPublished() && is_array($document->getInitial()) && count($document->getInitial()) > 0){
            $queryBuilder = $documentManager->createQueryBuilder('PHPOrchestra\ModelBundle\Document\Status');
            $queryBuilder
                ->field('name')->notEqual($document->getName())
                ->field('initial')->in(
                    $document->getInitial()
                 );
            $statuses = $queryBuilder->getQuery()->execute();
            foreach($statuses as $status){
                $status->setInitial(array_diff($status->getInitial(), $document->getInitial()));
                $this->statuses[] = $status;
            }
        }
    }

    /**
     * @param PostFlushEventArgs $eventArgs
     *
     */
    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        if(!empty($this->statuses)) {
            $documentManager = $eventArgs->getDocumentManager();
            foreach ($this->statuses as $status) {
                $documentManager->persist($status);
            }
            $this->statuses = [];
            $documentManager->flush();
        }
    }

}
