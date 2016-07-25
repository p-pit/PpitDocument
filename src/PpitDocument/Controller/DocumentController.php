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

class DocumentController extends AbstractActionController
{
	public function indexAction()
	{
		$context = Context::getCurrent();
		if (!$context->isAuthenticated()) $this->redirect()->toRoute('home');
	
		$parent_id = $this->params()->fromRoute('parent_id', null);
		$menu = $context->getInstance()->specifications['menu'];
	
		return new ViewModel(array(
				'context' => $context,
				'config' => $context->getConfig(),
				'menu' => $menu,
				'parent_id' => $parent_id,
		));
	}
	
	public function getFilters($params)
	{
		$context = Context::getCurrent();
		 
		// Retrieve the query parameters
		$filters = array();
	
		foreach ($context->getInstance()->specifications['ppitDocument']['document/search']['main'] as $propertyId => $rendering) {
	
			$property = ($params()->fromQuery($propertyId, null));
			if ($property) $filters[$propertyId] = $property;
			$min_property = ($params()->fromQuery('min_'.$propertyId, null));
			if ($min_property) $filters['min_'.$propertyId] = $min_property;
			$max_property = ($params()->fromQuery('max_'.$propertyId, null));
			if ($max_property) $filters['max_'.$propertyId] = $max_property;
		}
	
		foreach ($context->getInstance()->specifications['ppitDocument']['document/search']['more'] as $propertyId => $rendering) {
			 
			$property = ($params()->fromQuery($propertyId, null));
			if ($property) $filters[$propertyId] = $property;
			$min_property = ($params()->fromQuery('min_'.$propertyId, null));
			if ($min_property) $filters['min_'.$propertyId] = $min_property;
			$max_property = ($params()->fromQuery('max_'.$propertyId, null));
			if ($max_property) $filters['max_'.$propertyId] = $max_property;
		}
	
		return $filters;
	}
	
	public function searchAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();

		$parent_id = $this->params()->fromRoute('parent_id', null);
		
		// Return the link list
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'parent_id' => $parent_id,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function getList()
	{
		// Retrieve the context
		$context = Context::getCurrent();

		$parent_id = (int) $this->params()->fromRoute('parent_id', null);
		$parent = Document::get($parent_id);
		
		$params = $this->getFilters($this->params());
		$major = $this->params()->fromQuery('major', 'name');
    	$dir = $this->params()->fromQuery('dir', 'ASC');
		$documents = Document::getList($parent, $params, $major, $dir);
	
		if (count($params) == 0) $mode = 'todo'; else $mode = 'search';
	
		// Return the link list
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'documents' => $documents,
				'parent' => $parent,
				'mode' => $mode,
				'params' => $params,
				'major' => $major,
				'dir' => $dir,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function listAction()
	{
		return $this->getList();
	}
	
	public function exportAction()
	{
		$view = $this->getList();
	
		include 'public/PHPExcel_1/Classes/PHPExcel.php';
		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';
	
		$workbook = new \PHPExcel;
		(new SsmlJournalViewHelper)->formatXls($workbook, $view);
		$writer = new \PHPExcel_Writer_Excel2007($workbook);
	
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=Fichier.xlsx ');
		$writer->save('php://output');
	}

	public function detailAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
	
		$id = (int) $this->params()->fromRoute('id', 0);
	
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'id' => $id,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function dropboxRegisterAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();
		require_once "vendor/dropbox/dropbox-sdk/lib/Dropbox/autoload.php";
	
		$appInfo = \Dropbox\AppInfo::loadFromJsonFile("config/autoload/dropbox.p-pit.fr.json");
		$webAuth = new \Dropbox\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
		$authorizeUrl = $webAuth->start();
/*		echo "1. Go to: " . $authorizeUrl . "<br>";
		echo "2. Click \"Allow\" (you might have to log in first).<br>";
		echo "3. Copy the authorization code.<br>";*/
/*		list($accessToken, $dropboxUserId) = $webAuth->finish('9CRvqMLXD40AAAAAAAAAwmg0ezXl70d8AUOxicoG--w');
		echo "Access Token: " . $accessToken . "<br>";
		list($accessToken, $dropboxUserId) = $webAuth->finish('9CRvqMLXD40AAAAAAAAAvqyegio40cQuqFlvC5MxNqY');*/
//		$dbxClient = new \Dropbox\Client('9CRvqMLXD40AAAAAAAAAw01AjFizF3WrpmnYhseJ8RXng1QXiokyfjKqLczV5aQ6', "PHP-Example/1.0");
		
		$view = new ViewModel(array());
		return $view;
	}

	public function indexOldAction() {

		// Retrieve the context
		$context = Context::getCurrent();

		// Get the children links for a given folder (Link::get returns community root if link $id is null or not accessible)
		$parent_id = (int) $this->params()->fromRoute('parent_id', null);
		$parent = Document::get($parent_id);
		$major = $this->params()->fromQuery('major', 'name');
    	$dir = $this->params()->fromQuery('dir', 'ASC');
		$documents = Document::getList($parent, $major, $dir, $parent_id);

		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'parent_id' => $parent_id,
				'documents' => $documents,
     			'major' => $major,
				'dir' => $dir,
    			'parent' => $parent,
		));
//		if ($context->isSpaMode()) $view->setTerminal(true);
		return $view;
	}

	public function addAction()
	{
		// Check the presence of the parent id
		$parent_id = (int) $this->params()->fromRoute('parent_id', 0);
	
		// Retrieve the context
		$context = Context::getCurrent();
	
		// Retrieve the parent
		$parent = Document::get($parent_id);
		$parent_id = $parent->id;
	
		$document = Document::instanciate($parent_id);
	
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
		$request = $this->getRequest();
		if ($request->isPost()) {
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($request->getPost());
	
			if ($csrfForm->isValid()) { // CSRF check
				$document->loadDataFromRequest($request, $parent_id);
				if ($document->type == 'directory') $document->save();
				else $document->saveFile();
				$message = 'OK';
			}
		}

		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'csrfForm' => $csrfForm,
				'message' => $message,
				'error' => $error,
				'parent_id' => $parent_id,
				'parent' => $parent,
				'document' => $document,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function updateAction()
	{
	
		// Retrieve the context
		$context = Context::getCurrent();
	
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) return $this->redirect()->toRoute('index'); 
		$document = Document::get($id);
	
		// Retrieve the parent
		$parent = Document::get($document->parent_id);

		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
		$request = $this->getRequest();
		if ($request->isPost()) {
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($request->getPost());
	
			if ($csrfForm->isValid()) { // CSRF check
				$document->loadDataFromRequest($request, $parent_id);
				if ($document->type == 'directory') $document->save();
				else $document->saveFile();
				$message = 'OK';
			}
		}
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'csrfForm' => $csrfForm,
				'message' => $message,
				'error' => $error,
				'id' => $id,
				'parent' => $parent,
				'document' => $document,
		));
		if ($context->isSpaMode()) $view->setTerminal(true);
		return $view;
	}

	public function display() {

		// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the document
		$id = (int) $this->params()->fromRoute('id', 0);
		$document = Document::get($id);
//		if (!$document->authorization) $this->redirect()->toRoute('index');
		$document->retrieveContent();

		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'document' => $document,
				'id' => $id,
		));
		return $view;
	}

	public function displayAction() {
		// Retrieve the context
		$context = Context::getCurrent();

		$view = $this->display();
		if ($context->isSpaMode()) $view->setTerminal(true);
		return $view;
	}

	public function displayContentAction() {

		// Retrieve the context
		$context = Context::getCurrent();

		$id = (int) $this->params()->fromRoute('id', 0);
		$target = $this->params()->fromRoute('target', '');
		$document = Document::get($id);
//		if (!$document->authorization) $this->redirect()->toRoute('index');
		$document->retrieveContent();
		// Instanciate the csrf form
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
		$request = $this->getRequest();
		if ($request->isPost()) {
		
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($request->getPost());
		
			if ($csrfForm->isValid()) { // CSRF check
/*var_dump("document_update_action : ".$request->getPost('document_update_action'));
var_dump("part_to_update : ".$request->getPost('part_to_update'));
var_dump("part_to_delete : ".$request->getPost('part_to_delete'));
var_dump("part_to_move : ".$request->getPost('part_to_move'));
var_dump("part_receiving : ".$request->getPost('part_receiving'));*/
				$document->loadContentFromRequest($request);
				
				// Atomically save
				try {
					$connection = Document::getTable()->getAdapter()->getDriver()->getConnection();
					$connection->beginTransaction();
//					$document->saveContent();
					$connection->commit();
			    	$message = 'OK';
				}
		    	catch (Exception $e) {
		    		$connection->rollback();
		    		throw $e;
		    	}
			}
		}

		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'document' => $document,
				'id' => $id,
				'target' => $target,
				'message' => $message,
				'error' => $error,
				'csrfForm' => $csrfForm,
		));
		$view->setTerminal(true);
		return $view;
	}
	
	public function updatePartAction() {
		// Retrieve the context
		$context = Context::getCurrent();
	
		$id = (int) $this->params()->fromRoute('id', 0);
		$documentPart = DocumentPart::get($id);
		$document = Document::get($documentPart->document_id);
		if (!$document->ownerLink->authorization) $this->redirect()->toRoute('index');
		
		// Instanciate the csrf form
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
		$request = $this->getRequest();
		if ($request->isPost()) {
	
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($request->getPost());
	
			if ($csrfForm->isValid()) { // CSRF check
				$documentPart->loadContentFromRequest($request);
	
				// Atomically save
				try {
					$connection = DocumentPart::getTable()->getAdapter()->getDriver()->getConnection();
					$connection->beginTransaction();
					$documentPart->save();
					$connection->commit();
					$message = 'OK';
				}
				catch (Exception $e) {
					$connection->rollback();
					throw $e;
				}
			}
		}
	
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'documentPart' => $documentPart,
				'document' => $document,
				'id' => $id,
				'message' => $message,
				'error' => $error,
				'csrfForm' => $csrfForm,
		));
		if ($context->isSpaMode()) $view->setTerminal(true);
		return $view;
	}

	public function downloadAction()
	{
		// Retrieve the context
		$context = Context::getCurrent();

		// Check the presence of the id parameter for the entity to download
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) return $this->redirect()->toRoute('index');

		// Retrieve the document and its parent directory
		$document = Document::getTable()->get($id);
	
		$file = 'data/documents/'.$document->id;
	
		$response = new Stream();
		$response->setStream(fopen($file, 'r'));
		$response->setStatusCode(200);
		$response->setStreamName(basename($file));
	
		$headers = new Headers();
		$headers->addHeaders(array(
				'Content-Disposition' => 'attachment; filename="' . $document->name .'"',
				'Content-Type' => $document->mime,
				'Content-Length' => filesize($file)
		));
		$response->setHeaders($headers);
		return $response;
	}

	public function pdfAction() {
		// Retrieve the context
		$context = Context::getCurrent();
		
		$id = (int) $this->params()->fromRoute('id', 0);
		$document = Document::get($id);
//		if (!$document->ownerLink->authorization) $this->redirect()->toRoute('index');
		$document->retrieveContent();
    	$pdf = new PdfModel();
    	$pdf->setOption('filename', $document->name);
    	$pdf->setOption("paperSize", "a4"); //Defaults to 8x11
 		$pdf->setOption("paperOrientation", "portrait"); //Defaults to portrait
     	$pdf->setVariables(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'document' => $document,
     	));
		return $pdf;
	}

	public function deleteAction()
	{
		// Check the presence of the id parameter for the entity to delete
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) return $this->redirect()->toRoute('index');
	
		// Retrieve the settings
		$context = Context::getCurrent();
	
		// Retrieve the link and its parent folder
		$document = Document::get($id);
	
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
		$request = $this->getRequest();
		if ($request->isPost()) {
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($request->getPost());
	
			if ($csrfForm->isValid()) { // CSRF check
			
    			// Atomicity
    			$connection = Document::getTable()->getAdapter()->getDriver()->getConnection();
    			$connection->beginTransaction();
    			try {
		    		// Delete the row
					$return = $document->delete($request->getPost('update_time'));
					if ($return != 'OK') {
						$connection->rollback();
						$error = $return;
					}
					else {
						$connection->commit();
						$message = $return;
					}
    			}
           	    catch (\Exception $e) {
	    			$connection->rollback();
	    			throw $e;
	    		}
			}
		}

		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'csrfForm' => $csrfForm,
				'message' => $message,
				'error' => $error,
				'id' => $id,
				'document' => $document,
				'parent' => $parent,
		));
		if ($context->isSpaMode()) $view->setTerminal(true);
		return $view;
	}

	public function approbationRequestAction() {
		// Retrieve the context
		$context = Context::getCurrent();
	
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) $this->redirect()->toRoute('index');

		$document = Document::get($id);
		if (!$document->ownerLink->authorization) $this->redirect()->toRoute('index');
		if (!$document) $this->redirect()->toRoute('index');

		$contact = new Vcard();

		// Retrieve the vcards
		$vcards = Vcard::visibleContactList('');
		
		// Instanciate the csrf form
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
		$request = $this->getRequest();
		if ($request->isPost()) {
	
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($request->getPost());
	
			if ($csrfForm->isValid()) { // CSRF check
    			$contact_id = $contact->loadData($request);
					
        		// Save the contact
	        	Vcard::getTable()->save($contact);

    			Token::getNew(array(
						array(
							'contact_id' => $contact_id,
							'authorized_route' => 'document/index',
							'authorized_param' => 'parent_id',
							'authorized_id' => $document->parent_id,
							'validity' => null,
						),
						array(
							'contact_id' => $contact_id,
							'authorized_route' => 'document/display',
							'authorized_param' => 'parent_id',
							'authorized_id' => $document->parent_id,
							'validity' => null,
						),
						array(
							'contact_id' => $contact_id,
							'authorized_route' => 'document/pdf',
							'authorized_param' => 'parent_id',
							'authorized_id' => $document->parent_id,
							'validity' => null,
						),
						array(
							'contact_id' => $contact_id,
							'authorized_route' => 'document/approve',
							'authorized_param' => 'id',
							'authorized_id' => $id,
							'validity' => '2 day',
						),
				));
				$message = 'OK';
			}
		}
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'id' => $id,
				'contact' => $contact,
				'vcards' => $vcards,
				'csrfForm' => $csrfForm,
				'message' => $message,
				'error' => $error,
		));
		if ($context->isSpaMode()) $view->setTerminal(true);
		return $view;
	}

	public function approveAction() {
		// Retrieve the context
		$context = Context::getCurrent();
		
		$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) $this->redirect()->toRoute('index');
		$document = Document::get($id);
		if (!$document->ownerLink->authorization) $this->redirect()->toRoute('index');
		$document->retrieveContent();

		// Instanciate the csrf form
		$csrfForm = new CsrfForm();
		$csrfForm->addCsrfElement('csrf');
		$message = null;
		$error = null;
		$request = $this->getRequest();
		if ($request->isPost()) {
		
			$csrfForm->setInputFilter((new Csrf('csrf'))->getInputFilter());
			$csrfForm->setData($request->getPost());
			 
			if ($csrfForm->isValid()) { // CSRF check
				$document->loadDataFromRequest($request);
				// Atomically save
				$connection = Document::getTable()->getAdapter()->getDriver()->getConnection();
				$connection->beginTransaction();
				try {
					$document->save();
					$connection->commit();
					$message = 'OK';
				}
				catch (\Exception $e) {
					$connection->rollback();
					throw $e;
				}
			}
		}
		$view = new ViewModel(array(
				'context' => $context,
				'config' => $context->getconfig(),
				'document' => $document,
				'csrfForm' => $csrfForm,
				'message' => $message,
				'error' => $error,
		));
		if ($context->isSpaMode()) $view->setTerminal(true);
		return $view;
	}
}
