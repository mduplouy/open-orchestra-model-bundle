<?php

namespace OpenOrchestra\ModelBundle\Form\DataTransformer;

use OpenOrchestra\ModelInterface\Model\EmbedStatusInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class EmbedStatusToStatusTransformer
 */
class EmbedStatusToStatusTransformer implements DataTransformerInterface
{
    protected $statusRepositoy;
    protected $embedStatusClass;

    /**
     * @param StatusRepositoryInterface $statusRepository
     * @param string                    $embedStatusClass
     */
    public function __construct(StatusRepositoryInterface $statusRepository, $embedStatusClass)
    {
        $this->statusRepositoy = $statusRepository;
        $this->embedStatusClass = $embedStatusClass;
    }

    /**
     * @param EmbedStatusInterface $value
     *
     * @return StatusInterface
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        if ($value instanceof EmbedStatusInterface) {
            return $this->statusRepositoy->find($value->getId());
        }

        return '';
    }

    /**
     * @param StatusInterface $value
     *
     * @return EmbedStatusInterface
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        if ($value === null) {
            return null;
        }

        $embedStatusClass = $this->embedStatusClass;

        return $embedStatusClass::createFromStatus($value);
    }
}
