<?php
namespace PpitDocument\Controller;

use DOMPDFModule\View\Model\PdfModel;
use PpitContact\Model\Vcard;
use PpitCore\Form\CsrfForm;
use PpitCore\Model\Context;
use PpitCore\Model\Csrf;
use PpitDocument\Model\Document;
use PpitDocument\Model\DocumentPart;
use PpitUser\Model\Token;
use Zend\Http\Headers;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
		return $view;
	}

    public function homeAction()
    {
    	$context = Context::getCurrent();

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
				'description' => $homeSpecs['description'][$context->getLocale()],
    			'homeSpecs' => $homeSpecs,
    			'documents' => $documents,
    	));
    }
}
