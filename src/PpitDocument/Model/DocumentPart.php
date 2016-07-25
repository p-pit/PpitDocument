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
    public $document_id;
    public $content;
    public $saved_content;
    public $is_undone;
    public $image;
    public $next_part_id;
    public $audit = array();
    public $update_time;
    protected $inputFilter;

    // Transient fields
    public $comment;

    // Static fields
    private static $table;
    
	public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->document_id = (isset($data['document_id'])) ? $data['document_id'] : null;
        $this->content = (isset($data['content'])) ? $data['content'] : null;
        $this->saved_content = (isset($data['saved_content'])) ? $data['saved_content'] : null;
        $this->is_undone = (isset($data['is_undone'])) ? $data['is_undone'] : null;
        $this->image = (isset($data['image'])) ? json_decode($data['image'], true) : array();
        $this->next_part_id = (isset($data['next_part_id'])) ? $data['next_part_id'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'], true) : null;
        $this->lock = (isset($data['lock'])) ? $data['lock'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;
    }

    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['document_id'] = (int) $this->document_id;
    	$data['content'] = $this->content;
    	$data['saved_content'] = $this->saved_content;
    	$data['is_undone'] = (int) $this->is_undone;
    	$data['image'] = json_encode($this->image);
    	$data['next_part_id'] = (int) $this->next_part_id;
    	$data['audit'] = json_encode($this->audit);
    	$data['lock'] = (boolean) $this->lock;
    	return $data;
    }

    public static function get($id)
    {
		return DocumentPart::getTable()->get($id);
    }

    public function loadContentFromRequest($request)
    {
		if ($request->getPost('document_update_part_action') == 'update') {
	    	// Part update requested
			$this->saved_content = $this->content;
			$this->content = $request->getPost('editor_'.$this->id);
			$this->isUndone = false;
		}
   		elseif ($request->getPost('document_update_part_action') == 'undo') {
	    	// Part undo requested
	    	$saved = $this->content;
			$this->content = $this->saved_content;
			$this->saved_content = $saved;
			$this->is_undone = true;
		}
      	elseif ($request->getPost('document_update_part_action') == 'redo') {
	    	// Part redo requested
	    	$saved = $this->content;
			$this->content = $this->saved_content;
			$this->saved_content = $saved;
			$this->is_undone = false;
		}
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
