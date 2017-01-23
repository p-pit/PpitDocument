<?php
namespace PpitDocument\Controller;

use DOMPDFModule\View\Model\PdfModel;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitCore\Model\Token;
use PpitCore\Model\Vcard;
use PpitDocument\Model\Document;
use PpitDocument\Model\DocumentPart;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Log\Formatter\FirePhp;

class PublicController extends AbstractActionController
{
	public function displayContentAction() {
	
		// Retrieve the context
		$context = Context::getCurrent();
		$directory = $this->params()->fromRoute('directory', 0);
		$name = $this->params()->fromRoute('name', 0);
		$content = $context->getInstance()->specifications['ppitDocument']['pages'][$directory][$name];
		
		$document = Document::getWithPath('home/public/'.$directory.'/'.$name);
		$document->retrieveContent();

		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'directory' => $directory,
				'name' => $name,
				'content' => $content,
				'document' => $document,
				'description' => $document->properties['description'],
		));
		$view->setTerminal(true);
		return $view;
	}

	public function displayPageAction() {
		// Retrieve the context
		$context = Context::getCurrent();

		$directory = $this->params()->fromRoute('directory', 0);
		$name = $this->params()->fromRoute('name', 0);
		$specifications = $context->getInstance()->specifications['ppitDocument']['pages'][$directory][$name];
		
		$document = Document::getWithPath('home/public/'.$directory.'/'.$name);
		$document->retrieveContent();
		$credentials = array();
		if (array_key_exists('credentials', $specifications)) {
			foreach ($specifications['credentials'] as $identifier => $unused) $credentials[$identifier] = Document::getWithPath('home/public/credentials/'.$identifier);
		}

		$request = $this->getRequest();
		$fqdn = $request->getUri()->getHost();
		
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'fqdn' => $fqdn,
				'directory' => $directory,
				'name' => $name,
				'specifications' => $specifications,
				'document' => $document,
				'credentials' => $credentials,
				'description' => $document->properties['description'],
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
	
		$document = Document::getWithPath('home/public/'.$directory.'/'.$name);
		$document->retrieveContent();
		$credentials = array();
		if (array_key_exists('credentials', $content)) {
			foreach ($content['credentials'] as $identifier => $unused) $credentials[$identifier] = Document::getWithPath('home/public/credentials/'.$identifier);
		}
		
		$entryList = array();
		foreach ($context->getInstance()->specifications['ppitDocument']['pages'][$directory] as $entryId => $unused) {
			if ($entryId == $name) $entryList[$entryId] = $document;
			else $entryList[$entryId] = Document::getWithPath('home/public/blog/'.$entryId);
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

    	$request = $this->getRequest();
    	$fqdn = $request->getUri()->getHost();

		$homeSpecs = $context->getconfig('ppitDocument')['home'];
		$documents = array();

		$documents['jumbotron'] = Document::getWithPath('home/public/'.$homeSpecs['jumbotron']['directory'].'/'.$homeSpecs['jumbotron']['name']);
		$documents['jumbotron']->retrieveContent();
		
		$documents['frontProducts'] = array();
		foreach ($homeSpecs['frontProducts'] as $frontProductId => $frontProduct) {
			$documents['frontProducts'][$frontProductId] = Document::getWithPath('home/public/'.$homeSpecs['frontProducts'][$frontProductId]['directory'].'/'.$homeSpecs['frontProducts'][$frontProductId]['name']);
			$documents['frontProducts'][$frontProductId]->retrieveContent();
		}

		$documents['legalNotices'] = Document::getWithPath('home/public/'.$homeSpecs['legalNotices']['directory'].'/'.$homeSpecs['legalNotices']['name']);
		$documents['legalNotices']->retrieveContent();

    	return new ViewModel(array(
    			'context' => $context,
    			'config' => $context->getConfig(),
    			'fqdn' => $fqdn,
				'description' => $homeSpecs['description'][$context->getLocale()],
    			'homeSpecs' => $homeSpecs,
    			'documents' => $documents,
    			'robots' => 'index, follow',
    			'homePage' => true,
    	));
    }
}
