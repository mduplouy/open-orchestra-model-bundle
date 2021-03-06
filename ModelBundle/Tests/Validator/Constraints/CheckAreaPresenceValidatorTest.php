<?php

namespace OpenOrchestra\ModelBundle\Tests\Validator\Constraints;

use Phake;
use OpenOrchestra\ModelBundle\Validator\Constraints\CheckAreaPresence;
use OpenOrchestra\ModelBundle\Validator\Constraints\CheckAreaPresenceValidator;

/**
 * Class CheckAreaPresenceValidatorTest
 */
class CheckAreaPresenceValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CheckAreaPresenceValidator
     */
    protected $validator;

    protected $node;
    protected $areas;
    protected $context;
    protected $constraint;

    /**
     * Set up the test
     */
    public function setUp()
    {
        $this->constraint = new CheckAreaPresence();
        $this->context = Phake::mock('Symfony\Component\Validator\Context\ExecutionContext');
        $this->areas = Phake::mock('Doctrine\Common\Collections\ArrayCollection');

        $this->node = Phake::mock('OpenOrchestra\ModelInterface\Model\NodeInterface');
        Phake::when($this->node)->getAreas()->thenReturn($this->areas);

        $this->validator = new CheckAreaPresenceValidator($this->translator);
        $this->validator->initialize($this->context);
    }

    /**
     * Test instance
     */
    public function testClass()
    {
        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintValidator', $this->validator);
    }

    /**
     * @param int $count
     * @param int $violationTimes
     *
     * @dataProvider provideCountAndViolation
     */
    public function testAddViolationOrNot($count, $violationTimes)
    {
        Phake::when($this->areas)->count()->thenReturn($count);

        $this->validator->validate($this->node, $this->constraint);

        Phake::verify($this->context, Phake::times($violationTimes))->addViolationAt('nodeSource', $this->constraint->message);
        Phake::verify($this->context, Phake::times($violationTimes))->addViolationAt('templateId', $this->constraint->message);
    }

    /**
     * @return array
     */
    public function provideCountAndViolation()
    {
        return array(
            array(1, 0),
            array(0, 1),
        );
    }
}
