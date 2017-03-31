<?php
namespace PpitDocument\Model;

use PpitCore\Model\Context;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class DocumentPart implements InputFilterAwareInterface
{
    public $id;
    public $instance_id;
    public $status;
    public $document_id;
    public $content;
    public $content_locale_1;
    public $content_locale_2;
    public $image;
    public $image_locale_1;
    public $image_locale_2;
    public $next_part_id;
    public $audit = array();
    public $update_time;
    protected $inputFilter;

    // Deprecated
    public $content_en_us;
    public $content_fr_fr;
    public $image_en_us;
    public $image_fr_fr;
    public $saved_content;
    public $is_undone;

    // Static fields
    private static $table;
    
	public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->document_id = (isset($data['document_id'])) ? $data['document_id'] : null;
        $this->content = (isset($data['content'])) ? $data['content'] : null;
        $this->content_locale_1 = (isset($data['content_locale_1'])) ? $data['content_locale_1'] : null;
        $this->content_locale_2 = (isset($data['content_locale_2'])) ? $data['content_locale_2'] : null;
        $this->image = (isset($data['image'])) ? json_decode($data['image'], true) : array();
        $this->image_locale_1 = (isset($data['image_locale_1'])) ? json_decode($data['image_locale_1'], true) : array();
        $this->image_locale_2 = (isset($data['image_locale_2'])) ? json_decode($data['image_locale_2'], true) : array();
        $this->next_part_id = (isset($data['next_part_id'])) ? $data['next_part_id'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

        // Deprecated
        $this->content_en_us = (isset($data['content_en_us'])) ? $data['content_en_us'] : null;
        $this->content_fr_fr = (isset($data['content_fr_fr'])) ? $data['content_fr_fr'] : null;
        $this->saved_content = (isset($data['saved_content'])) ? $data['saved_content'] : null;
        $this->is_undone = (isset($data['is_undone'])) ? $data['is_undone'] : null;
        $this->image_en_us = (isset($data['image_en_us'])) ? json_decode($data['image_en_us'], true) : array();
        $this->image_fr_fr = (isset($data['image_fr_fr'])) ? json_decode($data['image_fr_fr'], true) : array();
    }

    public function getProperties()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['document_id'] = (int) $this->document_id;
    	$data['content'] = $this->content;
    	$data['content_en_us'] = $this->content_en_us;
    	$data['content_fr_fr'] = $this->content_fr_fr;
    	$data['saved_content'] = $this->saved_content;
    	$data['is_undone'] = (int) $this->is_undone;
    	$data['image'] = $this->image;
    	$data['image_en_us'] = $this->image_en_us;
    	$data['image_fr_fr'] = $this->image_fr_fr;
    	$data['next_part_id'] = (int) $this->next_part_id;
    	$data['audit'] = $this->audit;
    	return $data;
    }

    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['image'] = json_encode($this->image);
    	$data['image_en_us'] = json_encode($this->image_en_us);
    	$data['image_fr_fr'] = json_encode($this->image_fr_fr);
    	$data['audit'] = json_encode($this->audit);
    	return $data;
    }
    
    public static function get($id)
    {
		return DocumentPart::getTable()->get($id);
    }

    public function loadData($data)
    {
    	$context = Context::getCurrent();
    	$auditRow = array(
    			'time' => Date('Y-m-d G:i:s'),
    			'n_fn' => $context->getFormatedName(),
    	);
    	if (array_key_exists('status', $data)) {
    		$status = trim(strip_tags($data['status']));
    		if ($status == '' || strlen($status) > 255) return 'Integrity';
    		if ($this->status != $status) $auditRow['status'] = $this->status = $status;
    	}
        if (array_key_exists('content', $data)) {
    		$content = $data['content'];
    		if ($this->content != $content) $auditRow['content'] = $this->content = $content;
    	}
        if (array_key_exists('content_locale_1', $data)) {
    		$content_locale_1 = $data['content_locale_1'];
    		if ($this->content_locale_1 != $content_locale_1) $auditRow['content_locale_1'] = $this->content_locale_1 = $content_locale_1;
    	}
        if (array_key_exists('content_locale_2', $data)) {
    		$content_locale_2 = $data['content_locale_2'];
    		if ($this->content_locale_2 != $content_locale_2) $auditRow['content_locale_2'] = $this->content_locale_2 = $content_locale_2;
    	}
    	if (array_key_exists('image', $data)) {
    		$image = $data['image'];
    		if ($this->image != $image) $auditRow['image'] = $this->image = $image;
    	}
    	if (array_key_exists('image_locale_1', $data)) {
    		$image_locale_1 = $data['image_locale_1'];
    		if ($this->image_locale_1 != $image_locale_1) $auditRow['image_locale_1'] = $this->image_locale_1 = $image_locale_1;
    	}
    	if (array_key_exists('image_locale_2', $data)) {
    		$image_locale_2 = $data['image_locale_2'];
    		if ($this->image_locale_2 != $image_locale_2) $auditRow['image_locale_2'] = $this->image_locale_2 = $image_locale_2;
    	}
     	$this->audit[] = $auditRow;
    	return 'OK';
    }
    
	public function save()
	{
		DocumentPart::getTable()->save($this);
	}

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        throw new \Exception("Not used");
    }

    public static function getTable()
    {
    	if (!DocumentPart::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		DocumentPart::$table = $sm->get('PpitDocument\Model\DocumentPartTable');
    	}
    	return DocumentPart::$table;
    }
}
