<?php

declare(strict_types=1);

namespace SAML2\XML\mdui;

use SAML2\DOMDocumentFactory;
use SAML2\XML\mdui\DiscoHints;
use SAML2\XML\mdui\Keywords;
use SAML2\Utils;

/**
 * Class \SAML2\XML\mdrpi\DiscoHintsTest
 */
class DiscoHintsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test marshalling a basic DiscoHints element
     */
    public function testMarshalling()
    {
        $discoHints = new DiscoHints();
        $discoHints->setIPHint(["192.168.6.0/24", "fd00:0123:aa:1001::/64"]);
        $discoHints->setDomainHint(["example.org", "student.example.org"]);
        $discoHints->setGeolocationHint(["geo:47.37328,8.531126", "geo:19.34343,12.342514"]);

        $document = DOMDocumentFactory::fromString('<root />');
        $xml = $discoHints->toXML($document->firstChild);

        $discoElements = Utils::xpQuery(
            $xml,
            '/root/*[local-name()=\'DiscoHints\' and namespace-uri()=\'urn:oasis:names:tc:SAML:metadata:ui\']'
        );
        $this->assertCount(1, $discoElements);
        $discoElement = $discoElements[0];

        $ipHintElements = Utils::xpQuery(
            $discoElement,
            './*[local-name()=\'IPHint\' and namespace-uri()=\'urn:oasis:names:tc:SAML:metadata:ui\']'
        );
        $this->assertCount(2, $ipHintElements);
        $this->assertEquals("192.168.6.0/24", $ipHintElements[0]->textContent);
        $this->assertEquals("fd00:0123:aa:1001::/64", $ipHintElements[1]->textContent);

        $domainHintElements = Utils::xpQuery(
            $discoElement,
            './*[local-name()=\'DomainHint\' and namespace-uri()=\'urn:oasis:names:tc:SAML:metadata:ui\']'
        );
        $this->assertCount(2, $domainHintElements);
        $this->assertEquals("example.org", $domainHintElements[0]->textContent);
        $this->assertEquals("student.example.org", $domainHintElements[1]->textContent);

        $geoHintElements = Utils::xpQuery(
            $discoElement,
            './*[local-name()=\'GeolocationHint\' and namespace-uri()=\'urn:oasis:names:tc:SAML:metadata:ui\']'
        );
        $this->assertCount(2, $geoHintElements);
        $this->assertEquals("geo:47.37328,8.531126", $geoHintElements[0]->textContent);
        $this->assertEquals("geo:19.34343,12.342514", $geoHintElements[1]->textContent);
    }


    /**
     * Create an empty discoHints element
     */
    public function testMarshallingEmpty()
    {
        $discoHints = new DiscoHints();

        $document = DOMDocumentFactory::fromString('<root />');
        $xml = $discoHints->toXML($document->firstChild);

        $this->assertNull($xml);
    }


    /**
     * Test unmarshalling a basic DiscoHints element
     */
    public function testUnmarshalling()
    {
        $document = DOMDocumentFactory::fromString(<<<XML
<mdui:DiscoHints xmlns:mdui="urn:oasis:names:tc:SAML:metadata:ui">
  <mdui:IPHint>130.59.0.0/16</mdui:IPHint>
  <mdui:IPHint>2001:620::0/96</mdui:IPHint>
  <mdui:DomainHint>example.com</mdui:DomainHint>
  <mdui:DomainHint>www.example.com</mdui:DomainHint>
  <mdui:GeolocationHint>geo:47.37328,8.531126</mdui:GeolocationHint>
  <mdui:GeolocationHint>geo:19.34343,12.342514</mdui:GeolocationHint>
</mdui:DiscoHints>
XML
        );

        $disco = new DiscoHints($document->firstChild);

        $this->assertCount(2, $disco->getIPHint());
        $this->assertEquals('130.59.0.0/16', $disco->getIPHint()[0]);
        $this->assertEquals('2001:620::0/96', $disco->getIPHint()[1]);
        $this->assertCount(2, $disco->getDomainHint());
        $this->assertEquals('example.com', $disco->getDomainHint()[0]);
        $this->assertEquals('www.example.com', $disco->getDomainHint()[1]);
        $this->assertCount(2, $disco->getGeolocationHint());
        $this->assertEquals('geo:47.37328,8.531126', $disco->getGeolocationHint()[0]);
        $this->assertEquals('geo:19.34343,12.342514', $disco->getGeolocationHint()[1]);
    }


    /**
     * Add a Keywords element to the children attribute
     */
    public function testMarshallingChildren()
    {
        $discoHints = new DiscoHints();
        $keywords = new Keywords();
        $keywords->setLanguage("nl");
        $keywords->setKeywords(["voorbeeld", "specimen"]);
        $discoHints->setChildren([$keywords]);

        $document = DOMDocumentFactory::fromString('<root />');
        $xml = $discoHints->toXML($document->firstChild);

        $discoElements = Utils::xpQuery(
            $xml,
            '/root/*[local-name()=\'DiscoHints\' and namespace-uri()=\'urn:oasis:names:tc:SAML:metadata:ui\']'
        );
        $this->assertCount(1, $discoElements);
        $discoElement = $discoElements[0];
        $this->assertEquals("mdui:Keywords", $discoElement->firstChild->nodeName);
        $this->assertEquals("voorbeeld specimen", $discoElement->firstChild->textContent);
    }


    /**
     * Umarshal a DiscoHints attribute with extra children
     */
    public function testUnmarshallingChildren()
    {
        $document = DOMDocumentFactory::fromString(<<<XML
<mdui:DiscoHints xmlns:mdui="urn:oasis:names:tc:SAML:metadata:ui">
  <mdui:GeolocationHint>geo:47.37328,8.531126</mdui:GeolocationHint>
  <child1>content of tag</child1>
</mdui:DiscoHints>
XML
        );

        $disco = new DiscoHints($document->firstChild);

        $this->assertCount(1, $disco->getGeolocationHint());
        $this->assertEquals('geo:47.37328,8.531126', $disco->getGeolocationHint()[0]);
        $this->assertCount(1, $disco->getChildren());
        $this->assertEquals('content of tag', $disco->getChildren()[0]->getXML()->textContent);
    }
}
