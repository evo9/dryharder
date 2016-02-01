<?php

namespace Dryharder\Gateway\Components;

use DOMDocument;
use DOMElement;

class GenerateAgentXML
{
    const XML_VERSION = '1.0'; // версия xml по умолчанию
    const XML_ENCODING = 'utf-8'; // кодировка xml по умолчанию
    const ROOT = 'Agbis'; // корень XML по умолчанию

    public $xml;
    private $root;

    function __construct()
    {
        $this->xml = new DOMDocument($this::XML_VERSION, $this::XML_ENCODING);
        $this->root = $this->xml->createElement($this::ROOT);
        $this->xml->appendChild($this->root);
    }

    public function addEl($NodeKey, $text = null, DOMElement $parent = null)
    {
        $node = $text === null ? $this->xml->createElement($NodeKey) : $this->xml->createElement($NodeKey, $text);
        $parent == null ? $this->root->appendChild($node) : $parent->appendChild($node);

        return $node;
    }

    public function setAttr(DOMElement $node, $key, $value)
    {
        $node->setAttribute($key, $value);
    }

    public function saveXML()
    {
        return $this->xml->saveXML();
    }

    public function output()
    {
        header('Content-type: text/xml; charset=UTF-8');
        echo $this->saveXML();
    }

}
