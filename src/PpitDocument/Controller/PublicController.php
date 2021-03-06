<?php
namespace PpitDocument\Controller;

use DOMPDFModule\View\Model\PdfModel;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Community;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Document;
use PpitCore\Model\DocumentPart;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use PpitCommitment\Model\Account;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Log\Formatter\FirePhp;

class PublicController extends AbstractActionController
{
	public function displayPageAction() {
		// Retrieve the context
		$context = Context::getCurrent();
    	$place = Place::get($context->getPlaceId());
    	$directory = $this->params()->fromRoute('directory', 0);
		$name = $this->params()->fromRoute('name', 0);
		$request = $this->getRequest();
		$fqdn = $request->getUri()->getHost();
		$template = $context->getConfig('public/page/'.$directory)[$name];
		
		$this->layout('/layout/public-layout');
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'place' => $place,
				'fqdn' => $fqdn,
				'template' => $template,
				'directory' => $directory,
				'name' => $name,
    			'robots' => 'index, follow',
		));
		return $view;
	}

	public function displayBlogAction() {
	
		// Retrieve the context
		$context = Context::getCurrent();
		$directory = $this->params()->fromRoute('directory', 0);
		$name = $this->params()->fromRoute('name', 0);
		$content = $context->getInstance()->specifications['ppitDocument']['pages'][$directory][$name];
	
		$document = Document::getWithPath('root/public/'.$directory.'/'.$name);
		$document->retrieveContent();
		$credentials = array();
		if (array_key_exists('credentials', $content)) {
			foreach ($content['credentials'] as $identifier => $unused) $credentials[$identifier] = Document::getWithPath('home/public/credentials/'.$identifier);
		}
		
		$entryList = array();
		foreach ($context->getInstance()->specifications['ppitDocument']['pages'][$directory] as $entryId => $unused) {
			if ($entryId == $name) $entryList[$entryId] = $document;
			else $entryList[$entryId] = Document::getWithPath('root/public/blog/'.$entryId);
		}
		$request = $this->getRequest();
		$fqdn = $request->getUri()->getHost();
	
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'fqdn' => $fqdn,
				'directory' => $directory,
				'name' => $name,
				'content' => $content,
				'entryList' => $entryList,
				'document' => $document,
				'credentials' => $credentials,
				'description' => $document->properties['description'],
				'robots' => 'index, follow',
		));
		return $view;
	}
	
    public function homeAction()
    {
    	$context = Context::getCurrent();
    	$place = Place::get($context->getPlaceId());
    	
    	$request = $this->getRequest();
    	$fqdn = $request->getUri()->getHost();
		$template = $context->getConfig('public/home');
		$this->layout('/layout/public-layout');
		
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'place' => $place,
    			'fqdn' => $fqdn,
    			'template' => $template,
    			'robots' => 'index, follow',
    			'homePage' => true,
    	));
    	return $view;
    }
	
    public function communityHomeAction()
    {
    	$context = Context::getCurrent();
    	$id = $this->params()->fromRoute('id');
    	$community = Community::get($id);
    	$place = Place::get($community->place_id);
    	$subject_id = (int) $this->params()->fromRoute('subject_id');

    	$request = $this->getRequest();
    	$fqdn = $request->getUri()->getHost();
		$template = $context->getConfig('public/community-home/student');
		$this->layout('/layout/public-layout');
		
    	$view = new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'place' => $place,
    			'fqdn' => $fqdn,
    			'template' => $template,
    			'robots' => 'index, follow',
    			'homePage' => true,
		    	'subject_id' => $subject_id,
    	));
    	return $view;
    }
	
    public function communityHomePrintAction()
    {
    	return $this->communityHomeAction();
    }
}
