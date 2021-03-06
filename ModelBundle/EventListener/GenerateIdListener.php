<?php

namespace OpenOrchestra\ModelBundle\EventListener;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\Common\Annotations\AnnotationReader;
use OpenOrchestra\ModelInterface\Helper\SuppressSpecialCharacterHelperInterface;
use Symfony\Component\DependencyInjection\Container;
use OpenOrchestra\ModelInterface\Repository\FieldAutoGenerableRepositoryInterface;

/**
 * Class GenerateIdListener
 */
class GenerateIdListener
{
    protected $container;
    protected $annotationReader;
    protected $suppressSpecialCharacterHelper;

    /**
     * @param Container                               $container
     * @param AnnotationReader                        $annotationReader
     * @param SuppressSpecialCharacterHelperInterface $suppressSpecialCharacterHelper
     */
    public function __construct(Container $container, AnnotationReader $annotationReader, SuppressSpecialCharacterHelperInterface $suppressSpecialCharacterHelper)
    {
        $this->container = $container;
        $this->annotationReader = $annotationReader;
        $this->suppressSpecialCharacterHelper = $suppressSpecialCharacterHelper;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function prePersist(LifecycleEventArgs $event)
    {
        $document = $event->getDocument();
        $className = get_class($document);
        $generateAnnotations = $this->annotationReader->getClassAnnotation(new \ReflectionClass($className), 'OpenOrchestra\ModelInterface\Mapping\Annotations\Document');
        if (!is_null($generateAnnotations)) {
            $repository = $this->container->get($generateAnnotations->getServiceName());

            $getSource = $generateAnnotations->getSource($document);
            $getGenerated = $generateAnnotations->getGenerated($document);
            $setGenerated = $generateAnnotations->setGenerated($document);
            $testMethod = $generateAnnotations->getTestMethod();
            if ($testMethod === null && $repository instanceof FieldAutoGenerableRepositoryInterface) {
                $testMethod = 'testUniquenessInContext';
            }
            if (is_null($document->$getGenerated())) {
                $source = $document->$getSource();
                $source = Inflector::tableize($source);
                $sourceField = $this->suppressSpecialCharacterHelper->transform($source);
                $generatedField = $sourceField;
                $count = 1;
                while ($repository->$testMethod($generatedField)) {
                    $generatedField = $sourceField . '-' . $count;
                    $count++;
                }

                $document->$setGenerated($generatedField);
            }
        }
    }
}
