<?php

namespace OpenOrchestra\BackofficeBundle\Tests\EventListener;

use Phake;
use OpenOrchestra\ModelBundle\Document\Node;
use OpenOrchestra\ModelBundle\Document\Status;
use OpenOrchestra\ModelBundle\EventListener\SetInitialStatusListener;

/**
 * Class SetInitialStatusListenerTest
 */
class SetInitialStatusListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;
    protected $lifecycleEventArgs;
    protected $container;
    protected $statusRepository;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->statusRepository = Phake::mock('OpenOrchestra\ModelBundle\Repository\StatusRepository');
        $this->container = Phake::mock('Symfony\Component\DependencyInjection\Container');
        Phake::when($this->container)->get(Phake::anyParameters())->thenReturn($this->statusRepository);
        $this->lifecycleEventArgs = Phake::mock('Doctrine\ODM\MongoDB\Event\LifecycleEventArgs');

        $this->listener = new SetInitialStatusListener();
        $this->listener->setContainer($this->container);
    }

    /**
     * Test if method is present
     */
    public function testCallable()
    {
        $this->assertTrue(is_callable(array(
            $this->listener,
            'prePersist'
        )));
    }

    /**
     * @param Node   $node
     * @param Status $status
     *
     * @dataProvider provideNodeForPersist
     */
    public function testprePersist(Node $node, Status $status)
    {
        Phake::when($this->statusRepository)->findOneByInitial()->thenReturn($status);
        Phake::when($this->lifecycleEventArgs)->getDocument()->thenReturn($node);

        $this->listener->prePersist($this->lifecycleEventArgs);

        Phake::verify($node, Phake::times(1))->setStatus($status);
    }

    /**
     *
     * @return array
     */
    public function provideNodeForPersist()
    {
        $node = Phake::mock('OpenOrchestra\ModelBundle\Document\Node');
        $status = Phake::mock('OpenOrchestra\ModelBundle\Document\Status');

        return array(
            array($node, $status)
        );
    }
}
