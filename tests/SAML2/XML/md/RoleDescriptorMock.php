<?php

declare(strict_types=1);

namespace SAML2\XML\md;

use SAML2\Constants;

class RoleDescriptorMock extends \SAML2\XML\md\RoleDescriptor
{
    public function __construct(\DOMElement $xml = null)
    {
        parent::__construct('md:RoleDescriptor', $xml);
    }


    public function toXML(\DOMElement $parent) : \DOMElement
    {
        $xml = parent::toXML($parent);
        $xml->setAttributeNS(Constants::NS_XSI, 'xsi:type', 'myns:MyElement');
        $xml->setAttributeNS('http://example.org/mynsdefinition', 'myns:tmp', 'tmp');
        $xml->removeAttributeNS('http://example.org/mynsdefinition', 'tmp');
        return $xml;
    }
}
