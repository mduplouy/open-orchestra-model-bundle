<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;

/**
 * Class SavePublishedDocumentListener
 */
class SavePublishedDocumentListener
{
    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();
        if ($document instanceof StatusableInterface) {
            $status = $document->getStatus();
            $oldDocument = $eventArgs->getDocumentManager()->getUnitOfWork()->getOriginalDocumentData($document);
            if (array_key_exists('status', $oldDocument) && !is_null($oldDocument['status'])) {
                $oldStatus = $oldDocument['status'];
                if (!$oldStatus->isPublished()) {
                    return;
                }
            }
            if (!empty($status)
                 && $status->isPublished()
                 && (!method_exists($document, 'isDeleted') || ! $document->isDeleted())
            ) {
                     $documentManager = $eventArgs->getDocumentManager();
                     $documentManager->getUnitOfWork()->detach($document);
            }
        }
    }
}
