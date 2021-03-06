<?php

declare(strict_types=1);

namespace SAML2\Signature;

use SAML2\Configuration\IdentityProvider;
use SAML2\Signature\ValidatorChain;
use SAML2\Response;
use SAML2\Signature\MissingConfigurationException;

class ValidatorChainTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \SAML2\Signature\ValidatorChain
     */
    private $chain;


    public function setUp()
    {
        $this->chain = new ValidatorChain(new \Psr\Log\NullLogger(), []);
    }


    /**
     * @group signature
     *
     * @test
     */
    public function if_no_validators_can_validate_an_exception_is_thrown()
    {
        $this->chain->appendValidator(new MockChainedValidator(false, true));
        $this->chain->appendValidator(new MockChainedValidator(false, true));

        $this->expectException(MissingConfigurationException::class);
        $this->chain->hasValidSignature(new Response(), new IdentityProvider([]));
    }


    /**
     * @group signature
     *
     * @test
     */
    public function all_registered_validators_should_be_tried()
    {
        $this->chain->appendValidator(new MockChainedValidator(false, true));
        $this->chain->appendValidator(new MockChainedValidator(false, true));
        $this->chain->appendValidator(new MockChainedValidator(true, false));

        $validationResult = $this->chain->hasValidSignature(
            new Response(),
            new IdentityProvider([])
        );
        $this->assertFalse($validationResult, 'The validation result is not what is expected');
    }


    /**
     * @group signature
     *
     * @test
     */
    public function it_uses_the_result_of_the_first_validator_that_can_validate()
    {
        $this->chain->appendValidator(new MockChainedValidator(false, true));
        $this->chain->appendValidator(new MockChainedValidator(true, false));
        $this->chain->appendValidator(new MockChainedValidator(false, true));

        $validationResult = $this->chain->hasValidSignature(
            new Response(),
            new IdentityProvider([])
        );
        $this->assertFalse($validationResult, 'The validation result is not what is expected');
    }
}
