<?php
namespace PpitDocument;

use PpitDocument\Model\Document;
use PpitDocument\Model\DocumentPart;
use PpitCore\Model\GenericTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
            	'PpitDocument\Model\DocumentTable' =>  function($sm) {
                    $tableGateway = $sm->get('DocumentTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'DocumentTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Document());
                    return new TableGateway('document', $dbAdapter, null, $resultSetPrototype);
                },
            	'PpitDocument\Model\DocumentPartTable' =>  function($sm) {
                    $tableGateway = $sm->get('DocumentPartTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'DocumentPartTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new DocumentPart());
                    return new TableGateway('document_part', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
