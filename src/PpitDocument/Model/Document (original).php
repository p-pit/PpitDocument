<?php
namespace PpitCore\Model;

use PpitCore\Model\Context;
use PpitCore\Model\DocumentPart;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Document
{
    public $parts = array();
    
    public function __construct($id = 0)
    {
    	while ($id) {
    		$part = DocumentPart::getTable()->get($id);
    		$id = $part->next_item_id;
    		$this->parts[] = $part;
    	}
    }
}
