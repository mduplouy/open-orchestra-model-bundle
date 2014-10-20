<?php

namespace PHPOrchestra\ModelBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Blameable\Traits\BlameableDocument;
use Gedmo\Timestampable\Traits\TimestampableDocument;
use Gedmo\Mapping\Annotation as Gedmo;
use PHPOrchestra\ModelBundle\Model\FolderInterface;
use PHPOrchestra\ModelBundle\Model\SiteInterface;

/**
 * Class Folder
 *
 * @ODM\Document(
 *   collection="folder",
 *   repositoryClass="PHPOrchestra\ModelBundle\Repository\FolderRepository"
 * )
 * @ODM\InheritanceType("SINGLE_COLLECTION")
 * @ODM\DiscriminatorField("type")
 * @ODM\DiscriminatorMap({"media_folder"="MediaFolder"})
 */
abstract class Folder implements FolderInterface
{
    use BlameableDocument;
    use TimestampableDocument;

    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var FolderInterface
     *
     * @ODM\ReferenceOne(targetDocument="PHPOrchestra\ModelBundle\Document\Folder", inversedBy="subFolders")
     */
    protected $parent;

    /**
     * @var ArrayCollection
     *
     * @ODM\ReferenceMany(targetDocument="PHPOrchestra\ModelBundle\Document\Folder", mappedBy="parent")
     */
    protected $subFolders;

    /**
     * @var ArrayCollection
     *
     * @ODM\ReferenceMany(targetDocument="PHPOrchestra\ModelBundle\Document\Site", mappedBy="site")
     */
    protected $sites;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->subFolders = new ArrayCollection();
    }

    /**
     * @return string
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
     * @return FolderInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param FolderInterface $parent
     */
    public function setParent(FolderInterface $parent)
    {
        $this->parent = $parent;
        $parent->addSubFolder($this);
    }

    /**
     * @return Collection
     */
    public function getSubFolders()
    {
        return $this->subFolders;
    }

    /**
     * @param FolderInterface $subFolder
     */
    public function addSubFolder(FolderInterface $subFolder)
    {
        $this->subFolders->add($subFolder);
    }

    /**
     * @param FolderInterface $subFolder
     */
    public function removeSubFolder(FolderInterface $subFolder)
    {
        $this->subFolders->removeElement($subFolder);
    }

    /**
     * @return ArrayCollection
     */
    public function getSite()
    {
        return $this->sites;
    }

    /**
     * @param SiteInterface $site
     */
    public function addSite(SiteInterface $site)
    {
        $this->sites->add($site);
    }

    /**
     * @param SiteInterface $site
     */
    public function removeSite(SiteInterface $site)
    {
        $this->sites->remove($site);
    }
}
