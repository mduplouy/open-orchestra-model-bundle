<?php

namespace PHPOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use PHPOrchestra\ModelBundle\Model\ContentInterface;

/**
 * Class GenerateContentIdListener
 */
class GenerateContentIdListener
{
    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        if ( ($content = $event->getDocument()) instanceof ContentInterface
            && is_null($content->getContentId())
        ) {
            $content->setContentId($content->getId());
        }
    }
}
