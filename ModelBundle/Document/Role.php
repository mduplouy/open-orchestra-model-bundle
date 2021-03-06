<?php

namespace OpenOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use OpenOrchestra\ModelInterface\Model\StatusInterface;
use OpenOrchestra\ModelInterface\Model\TranslatedValueInterface;
use OpenOrchestra\ModelInterface\ModelTrait\TranslatedValueFilter;

/**
 * Class Role
 *
 * @ODM\Document(
 *   collection="role",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\RoleRepository"
 * )
 */
class Role implements RoleInterface
{
    use TranslatedValueFilter;

    /**
     * @ODM\Id()
     */
    protected $id;

    /**
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var StatusInterface
     *
     * @ODM\ReferenceOne(targetDocument="OpenOrchestra\ModelInterface\Model\StatusInterface", inversedBy="fromRoles")
     */
    protected $fromStatus;

    /**
     * @var StatusInterface
     *
     * @ODM\ReferenceOne(targetDocument="OpenOrchestra\ModelInterface\Model\StatusInterface", inversedBy="toRoles")
     */
    protected $toStatus;

    /**
     * @ODM\EmbedMany(targetDocument="OpenOrchestra\ModelInterface\Model\TranslatedValueInterface")
     */
    protected $descriptions;

    /**
     * Construct the class
     */
    public function __construct()
    {
        $this->descriptions = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return StatusInterface
     */
    public function getToStatus()
    {
        return $this->toStatus;
    }

    /**
     * @param StatusInterface $toStatus
     */
    public function setToStatus(StatusInterface $toStatus)
    {
        $this->toStatus = $toStatus;
        $toStatus->addToRole($this);
    }

    /**
     * @return StatusInterface
     */
    public function getFromStatus()
    {
        return $this->fromStatus;
    }

    /**
     * @param StatusInterface $fromStatus
     */
    public function setFromStatus(StatusInterface $fromStatus)
    {
        $this->fromStatus = $fromStatus;
        $fromStatus->addFromRole($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param TranslatedValueInterface $description
     */
    public function addDescription(TranslatedValueInterface $description)
    {
        $this->descriptions->add($description);
    }

    /**
     * @param TranslatedValueInterface $description
     */
    public function removeDescription(TranslatedValueInterface $description)
    {
        $this->descriptions->removeElement($description);
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getDescription($language = 'en')
    {
        return $this->filterByLanguage($this->descriptions, $language);
    }

    /**
     * @return ArrayCollection
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

    /**
     * @return array
     */
    public function getTranslatedProperties()
    {
        return array('getDescriptions');
    }
}
