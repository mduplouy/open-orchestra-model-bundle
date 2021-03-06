<?php

namespace OpenOrchestra\ModelBundle\DataFixtures\MongoDB;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelBundle\Document\Site;
use OpenOrchestra\ModelBundle\Document\SiteAlias;
use OpenOrchestra\ModelInterface\Model\SchemeableInterface;

/**
 * Class LoadSiteData
 */
class LoadSiteData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $site1 = $this->getSite1();
        $manager->persist($site1);
        $this->addReference('site1', $site1);

        $site2 = $this->getSite2();
        $manager->persist($site2);
        $this->addReference('site2', $site2);

        $site3 = $this->getSite3();
        $manager->persist($site3);
        $this->addReference('site3', $site3);

        $manager->flush();
    }

    /**
     * @return Site
     */
    protected function getSite1()
    {
        $site1 = new Site();
        $site1->setSiteId('1');
        $site1->setName('First site');
        $site1->addAlias($this->generateSiteAlias('front.openorchestra.dev', 'fr'));
        $site1->addAlias($this->generateSiteAlias('front.openorchestra.dev', 'en'));
        $site1->addAlias($this->generateSiteAlias('front.openorchestra.inte', 'fr', true));
        $site1->addAlias($this->generateSiteAlias('front.openorchestra.inte', 'en'));
        $site1->setDeleted(false);
        $site1->setTheme($this->getReference('themePresentation'));
        $site1->addBlock('menu');

        return $site1;
    }

    /**
     * @return Site
     */
    protected function getSite2()
    {
        $site2 = new Site();
        $site2->setSiteId('2');
        $site2->setName('Demo site');
        $site2->addAlias($this->generateSiteAlias('demo.open-orchestra.com', 'fr', true));
        $site2->addAlias($this->generateSiteAlias('demo.open-orchestra.com', 'en'));
        $site2->addAlias($this->generateSiteAlias('demo.openorchestra.inte', 'fr'));
        $site2->addAlias($this->generateSiteAlias('demo.openorchestra.inte', 'en'));
        $site2->addAlias($this->generateSiteAlias('demo.openorchestra.dev', 'fr'));
        $site2->addAlias($this->generateSiteAlias('demo.openorchestra.dev', 'en'));
        $site2->setDeleted(false);
        $site2->setTheme($this->getReference('themePresentation'));
        $site2->addBlock('carrousel');
        $site2->addBlock('gallery');
        $site2->addBlock('footer');
        $site2->addBlock('menu');
        $site2->addBlock('sub_menu');
        $site2->addBlock('language_list');
        $site2->addBlock('tiny_mce_wysiwyg');
        $site2->addBlock('configurable_content');
        $site2->addBlock('content_list');
        $site2->addBlock('content');
        $site2->addBlock('media_list_by_keyword');
        $site2->addBlock('video');
        $site2->addBlock('gmap');
        $site2->addBlock('add_this');
        $site2->addBlock('audience_analysis');

        return $site2;
    }

    /**
     * @return Site
     */
    protected function getSite3()
    {
        $site3 = new Site();
        $site3->setSiteId('3');
        $site3->setName('Empty site');
        $site3->addAlias($this->generateSiteAlias('empty.openorchestra.inte', 'fr', true));
        $site3->addAlias($this->generateSiteAlias('empty.openorchestra.inte', 'en'));
        $site3->addAlias($this->generateSiteAlias('empty.openorchestra.dev', 'fr'));
        $site3->addAlias($this->generateSiteAlias('empty.openorchestra.dev', 'en'));
        $site3->setDeleted(true);
        $site3->setTheme($this->getReference('themePresentation'));
        $site3->addBlock('sample');

        return $site3;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 300;
    }

    /**
     * @param string $domainName
     * @param string $language
     * @param bool   $main
     * @param string $prefix
     *
     * @return SiteAlias
     */
    protected function generateSiteAlias($domainName, $language, $main = false, $prefix = null)
    {
        $siteAlias = new SiteAlias();
        $siteAlias->setDomain($domainName);
        $siteAlias->setLanguage($language);
        $siteAlias->setMain($main);
        $siteAlias->setPrefix($prefix);
        $siteAlias->setScheme(SchemeableInterface::SCHEME_HTTP);

        return $siteAlias;
    }
}
