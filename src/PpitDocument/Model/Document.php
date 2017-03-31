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
    public $instance_id;
    public $status;
    public $locale_1;
    public $locale_2;
    public $type;
    public $parent_id;
    public $name;
    public $acl;
    public $summary;
    public $summary_locale_1;
    public $summary_locale_2;
    public $image;
    public $image_locale_1;
    public $image_locale_2;
    public $first_part_id;
    public $mime;
    public $url;
 	public $properties;
    public $properties_locale_1;
    public $properties_locale_2;
    public $audit;
    public $update_time;
    
    // Transient properties
	public $authorization;
	public $parents;
    public $parts;
    public $files;
    public $destinationPath;
    
    // Deprecated
    public $community_id;
    public $lock;
    public $properties_en_us = array();
    public $properties_fr_fr = array();
    
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
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->locale_1 = (isset($data['locale_1'])) ? $data['locale_1'] : null;
        $this->locale_2 = (isset($data['locale_2'])) ? $data['locale_2'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->parent_id = (isset($data['parent_id'])) ? $data['parent_id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->acl = (isset($data['acl'])) ? json_decode($data['acl'],true) : null;
        $this->summary = (isset($data['summary'])) ? $data['summary'] : null;
        $this->summary_locale_1 = (isset($data['summary_locale_1'])) ? $data['summary_locale_1'] : null;
        $this->summary_locale_2 = (isset($data['summary_locale_2'])) ? $data['summary_locale_2'] : null;
        $this->image = (isset($data['image'])) ? json_decode($data['image'], true) : array();
        $this->image_locale_1 = (isset($data['image_locale_1'])) ? json_decode($data['image_locale_1'], true) : array();
        $this->image_locale_2 = (isset($data['image_locale_2'])) ? json_decode($data['image_locale_2'], true) : array();
        $this->first_part_id = (isset($data['first_part_id'])) ? $data['first_part_id'] : null;
        $this->mime = (isset($data['mime'])) ? $data['mime'] : null;
        $this->url = (isset($data['url'])) ? $data['url'] : null;
        $this->properties = (isset($data['properties'])) ? json_decode($data['properties'], true) : null;
        $this->properties_locale_1 = (isset($data['properties_locale_1'])) ? json_decode($data['properties_locale_1'], true) : null;
        $this->properties_locale_2 = (isset($data['properties_locale_2'])) ? json_decode($data['properties_locale_2'], true) : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'],true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

	    // Deprecated
        $this->community_id = (isset($data['community_id'])) ? $data['community_id'] : null;
        $this->lock = (isset($data['lock'])) ? $data['lock'] : null;
        $this->properties_en_us = (isset($data['properties_en_us'])) ? json_decode($data['properties_en_us'], true) : null;
        $this->properties_fr_fr = (isset($data['properties_fr_fr'])) ? json_decode($data['properties_fr_fr'], true) : null;
    }

    public function getProperties()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['instance_id'] = (int) $this->instance_id;
    	$data['status'] = $this->status;
    	$data['locale_1'] = $this->locale_1;
    	$data['locale_2'] = $this->locale_2;
    	$data['type'] = $this->type;
    	$data['parent_id'] = (int) $this->parent_id;
    	$data['name'] = $this->name;
    	$data['acl'] = $this->acl;
    	$data['summary'] = $this->summary;
    	$data['summary_locale_1'] = $this->summary_locale_1;
    	$data['summary_locale_2'] = $this->summary_locale_2;
    	$data['image'] = $this->image;
    	$data['image_locale_1'] = $this->image_locale_1;
    	$data['image_locale_2'] = $this->image_locale_2;
    	$data['first_part_id'] = (int) $this->first_part_id;
    	$data['mime'] = $this->mime;
    	$data['url'] = $this->url;
    	$data['properties'] = $this->properties;
    	$data['properties_locale_1'] = $this->properties_locale_1;
    	$data['properties_locale_2'] = $this->properties_locale_2;
    	$data['audit'] = $this->audit;
    	return $data;
    }
    
    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['acl'] = json_encode($this->acl);
    	$data['image'] = json_encode($this->image);
    	$data['image_locale_1'] = json_encode($this->image_locale_1);
    	$data['image_locale_2'] = json_encode($this->image_locale_2);
    	$data['properties'] = json_encode($this->properties);
    	$data['properties_locale_1'] = json_encode($this->properties_locale_1);
    	$data['properties_locale_2'] = json_encode($this->properties_locale_2);
    	$data['audit'] = json_encode($this->audit);

	    // Deprecated
    	$data['community_id'] = (int) $this->community_id;
    	$data['properties'] = json_encode($this->properties);
    	$data['lock'] = (boolean) $this->lock;
    	$data['properties_en_us'] = json_encode($this->properties_en_us);
    	$data['properties_fr_fr'] = json_encode($this->properties_fr_fr);
    
    	return $data;
    }

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
/*    	if (array_key_exists($context->getContactId(), $this->acl['contacts'])) {
    		$contactAccess = $this->acl['contacts'][$context->getContactId()];
    	}
    	else */$contactAccess = null;
    	 
/*    	if (array_key_exists($context->getCommunityId(), $this->acl['communities'])) {
    		$communityAccess = $this->acl['communities'][$context->getCommunityId()];
    	}
    	else */$communityAccess = null;
    
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

    	return $document;
    }
    
    public static function getWithPath($path)
    {
    	$path = explode('/', $path);
    	$parent_id = 0;
    	foreach ($path as $link) {
    		if ($link) {
	    		$select = Document::getTable()->getSelect()->where(array('parent_id' => $parent_id, 'name' => $link));
	    		$current = Document::getTable()->selectWith($select)->current();
	    		$parent_id = $current->id;
    		}
    	}
    	return $current;
    }

    public static function instanciate($parent_id = null)
    {
    	$document = new Document;
    	$document->parent_id = $parent_id;
    	$document->acl = array();
    	$document->image = array();
    	$document->image_locale_1 = array();
    	$document->image_locale_2 = array();
    	$document->properties = array();
    	$document->properties_locale_1 = array();
    	$document->properties_locale_2 = array();
    	$document->audit = array();
    	$document->parts = array();
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
		if (array_key_exists('locale_1', $data)) {
			$locale_1 = trim(strip_tags($data['locale_1']));
			if ($locale_1 == '' || strlen($locale_1) > 255) return 'Integrity';
			if ($this->locale_1 != $locale_1) $auditRow['locale_1'] = $this->locale_1 = $locale_1;
		}
		if (array_key_exists('locale_2', $data)) {
			$locale_2 = trim(strip_tags($data['locale_2']));
			if ($locale_2 == '' || strlen($locale_2) > 255) return 'Integrity';
			if ($this->locale_2 != $locale_2) $auditRow['locale_2'] = $this->locale_2 = $locale_2;
		}
		if (array_key_exists('type', $data)) {
			$type = trim(strip_tags($data['type']));
			if ($type == '' || strlen($type) > 255) return 'Integrity';
			if ($this->type != $type) $auditRow['type'] = $this->type = $type;
		}
		if (array_key_exists('parent_id', $data)) {
			$parent_id = $data['parent_id'];
			if ($this->parent_id != $parent_id) $auditRow['parent_id'] = $this->parent_id = $parent_id;
		}
		if (array_key_exists('name', $data)) {
			$name = trim(strip_tags($data['name']));
			if ($name == '' || strlen($name) > 255) return 'Integrity';
			if ($this->name != $name) $auditRow['name'] = $this->name = $name;
		}
		if (array_key_exists('acl', $data)) {
			$acl = $data['acl'];
			if ($this->acl != $acl) $auditRow['acl'] = $this->acl = $acl;
		}
		if (array_key_exists('summary', $data)) {
			$summary = $data['summary'];
			if ($this->summary != $summary) $auditRow['summary'] = $this->summary = $summary;
		}
		if (array_key_exists('summary_locale_1', $data)) {
			$summary_locale_1 = $data['summary_locale_1'];
			if ($this->summary_locale_1 != $summary_locale_1) $auditRow['summary_locale_1'] = $this->summary_locale_1 = $summary_locale_1;
		}
		if (array_key_exists('summary_locale_2', $data)) {
			$summary_locale_2 = $data['summary_locale_2'];
			if ($this->summary_locale_2 != $summary_locale_2) $auditRow['summary_locale_2'] = $this->summary_locale_2 = $summary_locale_2;
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
		if (array_key_exists('first_part_id', $data)) {
			$first_part_id = $data['first_part_id'];
			if ($this->first_part_id != $first_part_id) $auditRow['first_part_id'] = $this->first_part_id = $first_part_id;
		}
		if (array_key_exists('mime', $data)) {
			$mime = trim(strip_tags($data['mime']));
			if ($mime == '' || strlen($mime) > 255) return 'Integrity';
			if ($this->mime != $mime) $auditRow['mime'] = $this->mime = $mime;
		}
		if (array_key_exists('url', $data)) {
			$url = trim(strip_tags($data['url']));
			if ($url == '' || strlen($url) > 255) return 'Integrity';
			if ($this->url != $url) $auditRow['url'] = $this->url = $url;
		}
		if (array_key_exists('properties', $data)) {
			$properties = $data['properties'];
			if ($this->properties != $properties) $auditRow['properties'] = $this->properties = $properties;
		}
		if (array_key_exists('properties_locale_1', $data)) {
			$properties_locale_1 = $data['properties_locale_1'];
			if ($this->properties_locale_1 != $properties_locale_1) $auditRow['properties_locale_1'] = $this->properties_locale_1 = $properties_locale_1;
		}
		if (array_key_exists('properties_locale_2', $data)) {
			$properties_locale_2 = $data['properties_locale_2'];
			if ($this->properties_locale_2 != $properties_locale_2) $auditRow['properties_locale_2'] = $this->properties_locale_2 = $properties_locale_2;
		}
		$this->parts = array();
		if (array_key_exists('parts', $data)) {
			foreach ($data['parts'] as $part) {
				$documentPart = new DocumentPart;
				$documentPart->document_id = $this->id;
				$documentPart->loadData($part);
				$this->parts[] = $documentPart;
			}
		}
		$this->audit[] = $auditRow;
		return 'OK';
	}

	// Deprecated
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
			DocumentPart::getTable()->multipleDelete(array('document_id' => $this->id));
			$next_part_id = 0;
			for ($i = count($this->parts) - 1; $i >= 0; $i--) {
				$this->parts[$i]->next_part_id = $next_part_id;
				$this->parts[$i]->id = 0;
				$this->parts[$i]->id = DocumentPart::getTable()->save($this->parts[$i]);
				$next_part_id = $this->parts[$i]->id;
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
			$this->first_part_id = $next_part_id;
		}
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
	}
	
	/**
	 * Adds a new row in the database.
	 * @return string
	 */
	public function add()
	{
		$this->id = 0;
		Document::getTable()->save($this);
		$this->saveContent();
		Document::getTable()->save($this);
		return 'OK';
	}
	
	/**
	 * Update an existing row in the database.
	 * If $update_time is provided, an isolation check is performed, such that the current update time in the database is not greater than the one given as an argument.
	 * In such a case the methods does not affect the database and returns 'Isolation', otherwise it returns 'OK'.
	 * @param string $update_time
	 * @return string
	 */
	public function update($update_time)
	{
		$context = Context::getCurrent();
		$document = Document::get($this->id);
		if ($update_time && $document->update_time > $update_time) return 'Isolation';
		$this->saveContent();
		Document::getTable()->save($this);
		return 'OK';
	}
	
	public function saveFile($files, $compress = false, $dropbox = null) {
		$context = Context::getCurrent();
		$config = $context->getConfig();
		foreach ($files as $file) {
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
		
				$adapter = new \Zend\File\Transfer\Adapter\Http();
				if ($this->id) { // $link->id is 0 in demo mode
					// Create the file on the file system with $id as a name
					if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) $adapter->addFilter('Rename', 'data/documents/'.$this->id);
					if ($adapter->receive($file['name'])) {

						if ($context->getConfig()['ppitCoreSettings']['compressGifPngToJpg'] && ($extension == 'gif' || $extension == 'png')) {
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

	/**
	 * @param Interaction $interaction
	 * @return string
	 */
	public static function processInteraction($interaction)
	{
		$context = Context::getCurrent();
		$content = json_decode($interaction->content, true);
		$globalRc = 'OK';
		$newContent = array();
		foreach ($content as $data) {
			$connection = Document::getTable()->getAdapter()->getDriver()->getConnection();
			$connection->beginTransaction();
			try {
				if ($data['action'] == 'update' || $data['action'] == 'delete') $document = Document::getWithPath($data['path'].$data['name']);
				elseif ($data['action'] == 'add') $document = Document::instanciate();
				if ($data['action'] == 'delete') $rc = $document->delete(null);
				else {
					if (array_key_exists('path', $data)) {
						$parent = Document::getWithPath($data['path']);
						if ($parent) $data['parent_id'] = $parent->id;
					}
					if ($document->loadData($data) != 'OK') throw new \Exception('View error');
					if (!$document->id) $rc = $document->add();
					else $rc = $document->update(null);
					$data['result'] = $rc;
					if ($rc != 'OK') {
						$globalRc = 'partial';
						$connection->rollback();
					}
					else $connection->commit();
				}
			}
			catch (\Exception $e) {
				$connection->rollback();
				throw $e;
			}
			$newContent[] = $data;
		}
		$interaction->content = json_encode($newContent);
		$interaction->update(null);
		return $globalRc;
	}
	
	public function isUsed($object)
	{
		// Allow or not deleting a community
		if (get_class($object) == 'PpitCore\Model\Community') {
	    	$rootDoc = Document::getTable()->get($object->root_document_id);
	    	if (!$rootDoc) return false;
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
		if ($this->authorization != 'admin' && $this->authorization != 'write') return 'Unauthorized';
	
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
