<?php

declare(strict_types=1);

namespace SAML2\XML\shibmd;

use SAML2\DOMDocumentFactory;
use SAML2\XML\shibmd\Scope;
use SAML2\Utils;

/**
 * Class \SAML2\XML\shibmd\Scope
 */
class ScopeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Marshalling a scope in literal (non-regexp) form.
     */
    public function testMarshallingLiteral()
    {
        $scope = new Scope();
        $scope->setScope("example.org");
        $scope->setIsRegexpScope(false);

        $document = DOMDocumentFactory::fromString('<root />');
        $scopeElement = $scope->toXML($document->firstChild);

        $scopeElements = Utils::xpQuery($scopeElement, '/root/shibmd:Scope');
        $this->assertCount(1, $scopeElements);
        $scopeElement = $scopeElements[0];

        $this->assertEquals('example.org', $scopeElement->nodeValue);
        $this->assertEquals('urn:mace:shibboleth:metadata:1.0', $scopeElement->namespaceURI);
        $this->assertEquals('false', $scopeElement->getAttribute('regexp'));
    }


    /**
     * Marshalling a scope which does not specificy the value for
     * regexp explicitly (expect it to default to 'false').
     */
    public function testMarshallingImplicitRegexpValue()
    {
        $scope = new Scope();
        $scope->setScope("example.org");

        $document = DOMDocumentFactory::fromString('<root />');
        $scopeElement = $scope->toXML($document->firstChild);

        $scopeElements = Utils::xpQuery($scopeElement, '/root/shibmd:Scope');
        $this->assertCount(1, $scopeElements);
        $scopeElement = $scopeElements[0];

        $this->assertEquals('example.org', $scopeElement->nodeValue);
        $this->assertEquals('urn:mace:shibboleth:metadata:1.0', $scopeElement->namespaceURI);
        $this->assertEquals('false', $scopeElement->getAttribute('regexp'));
    }


    /**
     * Marshalling a scope which is in regexp form.
     */
    public function testMarshallingRegexp()
    {
        $scope = new Scope();
        $scope->setScope("^(.*\.)?example\.edu$");
        $scope->setIsRegexpScope(true);

        $document = DOMDocumentFactory::fromString('<root />');
        $scopeElement = $scope->toXML($document->firstChild);

        $scopeElements = Utils::xpQuery($scopeElement, '/root/shibmd:Scope');
        $this->assertCount(1, $scopeElements);
        $scopeElement = $scopeElements[0];

        $this->assertEquals('^(.*\.)?example\.edu$', $scopeElement->nodeValue);
        $this->assertEquals('urn:mace:shibboleth:metadata:1.0', $scopeElement->namespaceURI);
        $this->assertEquals('true', $scopeElement->getAttribute('regexp'));
    }


    /**
     * Unmarshalling a scope in literal (non-regexp) form.
     */
    public function testUnmarshallingLiteral()
    {
        $document = DOMDocumentFactory::fromString(
<<<XML
<shibmd:Scope regexp="false">example.org</shibmd:Scope>
XML
        );
        $scope = new Scope($document->firstChild);

        $this->assertEquals('example.org', $scope->getScope());
        $this->assertFalse($scope->isRegexpScope());
    }


    /**
     * Unmarshalling a scope that does not specify an explicit
     * regexp value (assumed to be false).
     */
    public function testUnmarshallingWithoutRegexpValue()
    {
        $document = DOMDocumentFactory::fromString(
<<<XML
<shibmd:Scope>example.org</shibmd:Scope>
XML
        );
        $scope = new Scope($document->firstChild);

        $this->assertEquals('example.org', $scope->getScope());
        $this->assertFalse($scope->isRegexpScope());
    }


    /**
     * Unmarshalling a scope in regexp form.
     */
    public function testUnmarshallingRegexp()
    {
        $document = DOMDocumentFactory::fromString(
<<<XML
<shibmd:Scope regexp="true">^(.*|)example.edu$</shibmd:Scope>
XML
        );
        $scope = new Scope($document->firstChild);

        $this->assertEquals('^(.*|)example.edu$', $scope->getScope());
        $this->assertTrue($scope->isRegexpScope());
    }
}
