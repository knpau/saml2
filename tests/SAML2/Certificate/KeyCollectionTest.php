<?php

declare(strict_types=1);

namespace SAML2\Certificate;

use SAML2\Certificate\KeyCollection;
use \SAML2\Exception\InvalidArgumentException;

class KeyCollectionTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @group certificate
     *
     * @test
     */
    public function testKeyCollectionAddWrongType()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $kc = new KeyCollection();
        $kc->add("not a key, just a string");
    }
}
