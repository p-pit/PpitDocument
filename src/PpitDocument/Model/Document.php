<?php
namespace PpitDocument\Model;

use PpitCore\Model\Community;
use PpitCore\Model\Context;
use PpitCore\Model\Generic;
use PpitDocument\Model\DocumentPart;
//use PpitCore\Model\Link;
use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Document implements InputFilterAwareInterface
{
    public $id;
    public $parent_id;
    public $type;
    public $name;
    public $acl = array('contacts' => array(), 'communities' => array());
    public $audit = array();
    public $lock;
    public $summary;
    public $image;
    public $first_part_id;
    public $mime;
    public $url;
    public $update_time;
    
    // Transient properties
	public $authorization;
	public $parents;
	public $comment;
    public $parts = array();
    public $files;
    public $destinationPath;
    
    // Deprecated
    public $properties = array();
    public $community_id;

    protected $inputFilter;
    
    // Static fields
    private static $table;

	public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->parent_id = (isset($data['parent_id'])) ? $data['parent_id'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->acl = (isset($data['acl'])) ? json_decode($data['acl'],true) : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'],true) : null;
        $this->lock = (isset($data['lock'])) ? $data['lock'] : null;
        $this->summary = (isset($data['summary'])) ? $data['summary'] : null;
        $this->image = (isset($data['image'])) ? json_decode($data['image'], true) : array();
        $this->first_part_id = (isset($data['first_part_id'])) ? $data['first_part_id'] : null;
        $this->mime = (isset($data['mime'])) ? $data['mime'] : null;
        $this->url = (isset($data['url'])) ? $data['url'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

	    // Deprecated
        $this->community_id = (isset($data['community_id'])) ? $data['community_id'] : null;
        $this->properties = (isset($data['properties'])) ? json_decode($data['properties'], true) : null;
    }

    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['parent_id'] = (int) $this->parent_id;
    	$data['type'] = $this->type;
    	$data['name'] = $this->name;
    	$data['acl'] = json_encode($this->acl);
    	$data['audit'] = json_encode($this->audit);
    	$data['lock'] = (boolean) $this->lock;
    	$data['summary'] = $this->summary;
    	$data['image'] = json_encode($this->image);
    	$data['first_part_id'] = (int) $this->first_part_id;
    	$data['mime'] = $this->mime;
    	$data['url'] = $this->url;
    	return $data;

	    // Deprecated
    	$data['community_id'] = (int) $this->community_id;
    	$data['properties'] = json_encode($this->properties);
    }
/*
    public function __construct()
    {
		$this->content = array();
    }*/

    public static function getDropboxClient() {
    	$context = Context::getCurrent();
 		if (array_key_exists('dropbox', $context->getConfig('ppitDocument'))) {
	    	require_once "vendor/dropbox/dropbox-sdk/lib/Dropbox/autoload.php";
			$dropbox = $context->getConfig('ppitDocument')['dropbox'];
	    	return new \Dropbox\Client($dropbox['credential'], $dropbox['clientIdentifier']);
 		}
    }
    
    protected function getParents($document, &$result) {
    	$context = Context::getCurrent();
    	if ($document) {
    		$parent = Document::getTable()->get($document->parent_id);
    		$this->getParents($parent, $result);
    		$result[] = $document;
    	}
    }
    
	public function retrieveAuthorization()
	{
    	$context = Context::getCurrent();

    	// Retrieve the contact and community access to this resource
    	if (array_key_exists($context->getContactId(), $this->acl['contacts'])) {
    		$contactAccess = $this->acl['contacts'][$context->getContactId()];
    	}
    	else $contactAccess = null;
    	 
    	if (array_key_exists($context->getCommunityId(), $this->acl['communities'])) {
    		$communityAccess = $this->acl['communities'][$context->getCommunityId()];
    	}
    	else $communityAccess = null;
    
    	// Give the highest-level right between contact and community
    	if ($communityAccess == 'admin' || $contactAccess == 'admin') $this->authorization = 'admin';
    	elseif ($communityAccess == 'write' || $contactAccess == 'write') $this->authorization = 'write';
    	elseif ($communityAccess == 'read' || $contactAccess == 'read') $this->authorization = 'read';
    	else $this->authorization = null;
    	return $this->authorization;
    }

    public static function getList($parent, $params, $major, $dir)
    {
		$select = Document::getTable()->getSelect()
			->order(array($major.' '.$dir, 'name', 'update_time DESC'));
		
		$where = new Where;

		// Todo list vs search modes
		if ($parent) {
			$where->equalTo('document.parent_id', $parent->id);
		}
		else {
		
			// Set the filters
			foreach ($params as $propertyId => $property) {
				if (substr($propertyId, 0, 4) == 'min_') $where->greaterThanOrEqualTo('document.'.substr($propertyId, 4), $params[$propertyId]);
				elseif (substr($propertyId, 0, 4) == 'max_') $where->lessThanOrEqualTo('document.'.substr($propertyId, 4), $params[$propertyId]);
				else $where->like('document.'.$propertyId, '%'.$params[$propertyId].'%');
			}
		}

		$select->where($where);
		$cursor = Document::getTable()->selectWith($select);
		$documents = array();
		foreach ($cursor as $document) {

			$document->retrieveAuthorization();

    		// Set the inherited authorization where unspecified
    		if (!$document->authorization) $document->authorization = $parent->authorization;

			/*if ($document->authorization)*/ $documents[$document->id] = $document;
		}

		return $documents;
    }
/*
    public static function instanciate($contents)
    {
    	$document = new Document;
    	$document->parts = array();
    	foreach ($contents as $content) {
    		$documentPart = new DocumentPart;
    		$documentPart->content = $content;
    		$document->parts[] = $documentPart;
    	}
    	return $document;
    }*/

    public static function get($id, $column = 'id')
    {
    	$context = Context::getCurrent();
		// Return the community's root document as a default
		if (!$id) {
			if (!$context->getCommunityId()) $document = Document::getTable()->get(0, 'parent_id');
			else {
				$community = Community::getTable()->get($context->getCommunityId());
				$document = Document::getTable()->get($community->root_document_id);
			}
		}
    	else $document = Document::getTable()->get($id, $column);

	    // Recursively retrieve the parents
	    $document->parents = array();
	    $document->getParents($document, $document->parents);

	    // Retrieve the most specific access right for this community or user on the parent resource
	    for ($i = count($document->parents)-1; $i >= 0; $i--) {
	    	$parent = Document::getTable()->get($document->parents[$i]->id);
	    	$authorization = $parent->retrieveAuthorization();
	    	if ($authorization) {
	    		$document->authorization = $authorization;
	    		break;
	    	}
	    }

	    // Return the community's root document if the requested resource is not allowed
/*	    if (!$document->authorization) {
	    	if (!$context->getCommunityId()) $document = Document::getTable()->get(0, 'parent_id');
	    	else {
	    		$community = Community::getTable()->get($context->getCommunityId());
	    		$document = Document::getTable()->get($community->root_document_id);
	    	}
	    }*/

    	return $document;
    }
    
    public static function getWithPath($path)
    {
    	$path = explode('/', $path);
    	$parent_id = 0;
    	foreach ($path as $link) {
    		$select = Document::getTable()->getSelect()->where(array('parent_id' => $parent_id, 'name' => $link));
    		$current = Document::getTable()->selectWith($select)->current();
    		$parent_id = $current->id;
    	}
    	return $current;
    }
    
    public static function instanciate($parent_id)
    {
		$document = new Document();
		$document->parent_id = $parent_id;
		return $document;
    }

    public function retrieveContent()
    {
    	$part_id = $this->first_part_id;
    	while ($part_id) {
    		$part = DocumentPart::getTable()->get($part_id);
    		$part_id = $part->next_part_id;
    		$this->parts[] = $part;
    	}
    }

	public function loadData($data, $files, $parent_id, $destinationPath = null)
	{
		$this->parent_id = $parent_id;
		if ($data['directory']) {
			$this->type = 'directory';
			$this->name = $data['directory'];
		}
		elseif ($data['uploaded']) {
			$this->type = 'uploaded';
			$this->files = $files;
			$this->name = $files['name']['name'];
			$this->destinationPath = $destinationPath;
		}
		elseif ($data['url']) {
			$this->type = 'link';
			$this->name = $data['name'];
			$this->url = $data['url'];
		}
		
/*		$this->comment = trim(strip_tags($data['comment']));
		if (strlen($this->comment) > 2047) return 'Integrity';

		$status = $data['action'];
		if ($status != 'Rejected' && $status != 'Approved') return 'Integrity';
		$this->audit[] = (object) array(
				'status' => $status,
				'time' => Date('Y-m-d h:m:s a'),
				'n_fn' => $context->getFormatedName(),
				'comment' => $this->comment,
		);*/
		return 'OK';
	}

	public function loadDataFromRequest($request, $parent_id, $destinationPath = null)
	{
		$files = $request->getFiles()->toArray();
		
		$data = array();
		$data['name'] = $request->getPost('name');
		$data['directory'] = $request->getPost('directory');
		$data['url'] = $request->getPost('url');
/*		$data['status'] = $request->getPost('status');
		$data['comment'] = $request->getPost('comment');
		$data['action'] = $request->getPost('action');*/
		$return = $this->loadData($data, $files, $parent_id, $destinationPath);
		if ($return != 'OK') throw new \Exception('View error');
	}

	public function loadContentFromRequest($request)
	{
		if ($request->getPost('action') == 'move') {
			$part_to_move = $request->getPost('part_to_move');
			$part_receiving = $request->getPost('part_receiving');
			$new_parts = array();
			for ($i = 0; $i < count($this->parts); $i++) {
				if ($i == $part_to_move);
				elseif ($i == $part_receiving) {
					$new_parts[] = $this->parts[$part_to_move];
					$new_parts[] = $this->parts[$i];
				}
				else $new_parts[] = $this->parts[$i];
			}
			$this->parts = $new_parts;
		}
		elseif ($request->getPost('part_to_delete') >= 0) {
			// Part deletion requested
			$new_parts = array();
			$i = 0;
			foreach ($this->parts as $part) {
				if ($i != $request->getPost('part_to_delete')) $new_parts[] = $part;
				$i++;
			}
			$this->parts = $new_parts;
		}
		elseif ($request->getPost('editor_'.$this->id) != '') {
			// Part addition requested
			$part = $this->addPart();
			$part->content = $request->getPost('editor_'.$this->id);
			$this->parts[] = $part;
		}
/*		else {
			// Part update requested
			for ($i = 0; $i < count($this->parts); $i++) {
				if ($i < count($this->parts)) {
					$part = $this->parts[$i];
					$part->content = $request->getPost('editor_'.$this->id.'_'.$i);
				}
			}
		}*/
	}

	public function addPart()
	{
		$part = new DocumentPart();
		$part->document_id = $this->id;
		return $part;
	}

	public function save()
	{
		return Document::getTable()->save($this);
	}

	public function saveFile($compress = false, $dropbox = null) {
		$context = Context::getCurrent();
		foreach ($this->files as $file) {
			if ($file['size'] > $context->getConfig()['ppitCoreSettings']['maxUploadSize']) $error = 'Size';
			else {
				$name = $file['name'];
				$extension = substr($name, strpos($name, '.')+1);
				$type = $file['type'];

				// Write the link in the database
				$this->owner_id = $context->getContactId();
				if (!$this->destinationPath && $compress && ($extension == 'gif' || $extension == 'png')) {
					$this->mime = 'image/jpeg';
					$this->name = ((strpos($name, '.')) ? substr($name, 0, strpos($name, '.')) : $name).'.jpg';
				} else {
					$this->mime = $type;
					$this->name = $name;
				}

				// Save a link record only in the case of the destination path is not given
				if (!$this->destinationPath) $this->id = Document::getTable()->save($this);
		
				$adapter = new \Zend\File\Transfer\Adapter\Http();
		
				if ($this->destinationPath || $this->id) { // $link->id is 0 in demo mode
					// Create the file on the file system with $id as a name
					if ($context->getInstanceId() != 0) $adapter->addFilter('Rename', ($this->destinationPath) ? $this->destinationPath : 'data/documents/'.$this->id);
					if ($this->destinationPath && file_exists($this->destinationPath)) unlink($this->destinationPath);
					if ($adapter->receive($file['name'])) {

						if (!$this->destinationPath && $context->getConfig()['ppitCoreSettings']['compressGifPngToJpg'] && ($extension == 'gif' || $extension == 'png')) {
							$src = 'data/documents/'.$this->id;
							$destination = 'data/documents/'.$this->id.'.jpg';
		
							// Compress the image
							$info = getimagesize($src);
							if ($info['mime'] == 'image/gif')
							{
								$image = imagecreatefromgif($src);
							}
							elseif ($info['mime'] == 'image/png')
							{
								$image = imageCreateFromPng($src);
							}
							//compress and save file to jpg
							imagejpeg($image, $destination, 75);
							unlink('data/documents/'.$this->id);
							rename('data/documents/'.$this->id.'.jpg', 'data/documents/'.$this->id);
						}
					
						if ($dropbox) {
    						require_once "vendor/dropbox/dropbox-sdk/lib/Dropbox/autoload.php";
				    		$dropboxSpecs = $context->getConfig('ppitDocument')['dropbox'];
				    		$dbxClient = new \Dropbox\Client($dropboxSpecs['credential'], $dropboxSpecs['clientIdentifier']);
							$f = fopen('data/documents/'.$this->id, "rb");
							$result = $dbxClient->uploadFile($dropbox.$this->name, \Dropbox\WriteMode::add(), $f);
							fclose($f);
						}
					}
				}
			}
		}
		return $this->id;
	}

	public function saveContent()
	{
		DocumentPart::getTable()->multipleDelete(array('document_id' => $this->id));
		$next_part_id = 0;
		for ($i = count($this->parts) - 1; $i >= 0; $i--) {
			$this->parts[$i]->next_part_id = $next_part_id;
			$this->parts[$i]->id = 0;
			$this->parts[$i]->id = DocumentPart::getTable()->save($this->parts[$i]);
			$next_part_id = $this->parts[$i]->id;
		}
		$this->first_part_id = $next_part_id;
		Document::getTable()->save($this);
	}

	public function add()
	{
		$this->id = 0;
		Document::getTable()->save($this);
		$this->saveContent();
		return $this->id;
	}
	
	public function isUsed($object)
	{
		// Allow or not deleting a community
		if (get_class($object) == 'PpitCore\Model\Community') {
	    	$rootDoc = Document::getTable()->get($object->root_document_id);
	    	if (!$rootDoc) return true;
    		if (Generic::getTable()->cardinality('document', array('parent_id' => $rootDoc->id)) > 0) return true;
		}
		return false;
	}
	
	public function isDeletable()
	{
		$context = Context::getCurrent();
	
		// Not deletable if the document is parent of other documents (a directory)
		if (Generic::getTable()->cardinality('document', array('parent_id' => $this->id)) > 0) return false;
	
		// Check other dependencies
		$config = $context->getConfig();
		foreach($config['ppitCoreDependencies'] as $dependency) {
			if ($dependency->isUsed($this)) return false;
		}

		return true;
	}
	
	public function delete($update_time)
	{
		$context = Context::getCurrent();
		$document = Document::get($this->id);
	
		// Access control : Restricted to the community in context perimeter
//		if ($this->authorization != 'admin' && $this->authorization != 'write') return 'Unauthorized';
	
		// Isolation check
		if ($update_time && $document->update_time > $update_time) return 'Isolation';
		Document::getTable()->delete($id);

		if ($this->type == 'uploaded') {
			// Delete the file on the file system
			unlink($file = 'data/documents/'.$id);
		}
			
		return 'OK';
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
    	if (!Document::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Document::$table = $sm->get('PpitDocument\Model\DocumentTable');
    	}
    	return Document::$table;
    }
}
