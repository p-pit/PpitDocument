<?php

return array(
    'controllers' => array(
        'invokables' => array(
        	'PpitDocument\Controller\Document' => 'PpitDocument\Controller\DocumentController',
        	'PpitDocument\Controller\Public' => 'PpitDocument\Controller\PublicController',
        ),
    ),
 
    'router' => array(
        'routes' => array(
        	'document' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/document',
                    'defaults' => array(
                        'controller' => 'Ppitdocument\Controller\Document',
                        'action'     => 'index',
                    ),
                ),
            	'may_terminate' => true,
            		'child_routes' => array(
	        				'index' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/index[/:parent_id]',
	        								'defaults' => array(
	        										'action' => 'index',
	        								),
	        						),
	        				),
	        				'search' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/search[/:parent_id]',
	        								'defaults' => array(
	        										'action' => 'search',
	        								),
	        						),
	        				),
	        				'list' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/list[/:parent_id]',
	        								'defaults' => array(
	        										'action' => 'list',
	        								),
	        						),
	        				),
	        				'export' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/export[/:parent_id]',
	        								'defaults' => array(
	        										'action' => 'export',
	        								),
	        						),
	        				),
	       					'detail' => array(
        								'type' => 'segment',
        								'options' => array(
        										'route' => '/detail[/:id]',
        										'constraints' => array(
        												'id' => '[0-9]*',
        										),
        										'defaults' => array(
        												'action' => 'detail',
        										),
        								),
        						),
            				'dropboxRegister' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/dropbox-register',
            								'defaults' => array(
            										'action' => 'dropboxRegister',
            								),
            						),
            				),
            				'indexOld' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/index-old[/:parent_id]',
            								'constraints' => array(
            										'parent_id' => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'indexOld',
            								),
            						),
            				),
            				'add' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/add[/:parent_id]',
            								'constraints' => array(
            										'parent_id' => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'add',
            								),
            						),
            				),
            				'update' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/update[/:id]',
            								'constraints' => array(
            										'id' => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'update',
            								),
            						),
            				),
            				'download' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/download[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'download',
            								),
            						),
            				),
            				'approbationRequest' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/approbation-request[/:id]',
            								'constraints' => array(
            										'id' => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'approbationRequest',
            								),
            						),
            				),
            				'approve' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/approve[/:id]',
            								'constraints' => array(
            										'id' => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'approve',
            								),
            						),
            				),
            				'display' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/display[/:id]',
            								'constraints' => array(
            										'id' => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'display',
            								),
            						),
            				),
            				'displayContent' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/display-content[/:id][/:target]',
            								'constraints' => array(
            										'id' => '[0-9]*',
											        'target' => '[a-zA-Z0-9_-]+',
            								),
            								'defaults' => array(
            										'action' => 'displayContent',
            								),
            						),
            				),
            				'updatePart' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/update-part[/:id]',
            								'constraints' => array(
            										'id' => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'updatePart',
            								),
            						),
            				),
            				'pdf' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/pdf[/:id]',
            								'constraints' => array(
            										'id' => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'pdf',
            								),
            						),
            				),
            				'delete' => array(
            						'type' => 'segment',
            						'options' => array(
            								'route' => '/delete[/:id]',
            								'constraints' => array(
            										'id'     => '[0-9]*',
            								),
            								'defaults' => array(
            										'action' => 'delete',
            								),
            						),
            				),
            		),
            ),
        	'public' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/public',
                    'defaults' => array(
                        'controller' => 'Ppitdocument\Controller\Public',
                        'action'     => 'displayPage',
                    ),
                ),
            	'may_terminate' => true,
            		'child_routes' => array(
	        				'displayContent' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => 'display-content[/:directory][/:name]',
            								'constraints' => array(
											        'directory' => '[a-zA-Z0-9_-]+',
											        'name' => '[a-zA-Z0-9_-]+',
            								),
	        								'defaults' => array(
	        										'action' => 'displayContent',
	        								),
	        						),
	        				),
            				'displayPage' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '[/:directory][/:name]',
            								'constraints' => array(
											        'directory' => '[a-zA-Z0-9_-]+',
											        'name' => '[a-zA-Z0-9_-]+',
            								),
	        								'defaults' => array(
	        										'action' => 'displayPage',
	        								),
	        						),
	        				),
	        				'home' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/home',
	        								'defaults' => array(
	        										'action' => 'home',
	        								),
	        						),
	        				),
            		),
        	),
        ),
    ),

	'bjyauthorize' => array(
		// Guard listeners to be attached to the application event manager
		'guards' => array(
			'BjyAuthorize\Guard\Route' => array(
				array('route' => 'document', 'roles' => array('user')),
				array('route' => 'document/index', 'roles' => array('user')),
				array('route' => 'document/search', 'roles' => array('user')),
				array('route' => 'document/export', 'roles' => array('user')),
            	array('route' => 'document/list', 'roles' => array('user')),
				array('route' => 'document/detail', 'roles' => array('user')),
				array('route' => 'document/add', 'roles' => array('user')),
				array('route' => 'document/approbationRequest', 'roles' => array('user')),
				array('route' => 'document/approve', 'roles' => array('user')),
				array('route' => 'document/display', 'roles' => array('admin')),
				array('route' => 'document/displayContent', 'roles' => array('guest')),
				array('route' => 'document/download', 'roles' => array('user')),
				array('route' => 'document/dropboxRegister', 'roles' => array('admin')),
				array('route' => 'document/indexOld', 'roles' => array('user')),
				array('route' => 'document/update', 'roles' => array('user')),
				array('route' => 'document/updatePart', 'roles' => array('guest')),
				array('route' => 'document/pdf', 'roles' => array('user')),
				array('route' => 'document/delete', 'roles' => array('user')),
				array('route' => 'public/displayContent', 'roles' => array('guest')),
				array('route' => 'public/displayPage', 'roles' => array('guest')),
				array('route' => 'public/home', 'roles' => array('guest')),
			)
		)
	),
		
    'view_manager' => array(
    	'strategies' => array(
    			'ViewJsonStrategy',
    	),
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',       // On défini notre doctype
        'not_found_template'       => 'error/404',   // On indique la page 404
        'exception_template'       => 'error/index', // On indique la page en cas d'exception
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
           'ppit-document' => __DIR__ . '/../view',
        ),
    ),
	'translator' => array(
		'locale' => 'fr_FR',
		'translation_file_patterns' => array(
			array(
				'type'     => 'phparray',
				'base_dir' => __DIR__ . '/../language',
				'pattern'  => '%s.php',
				'text_domain' => 'ppit-document'
			),
	       	array(
	            'type' => 'phpArray',
	            'base_dir' => './vendor/zendframework/zendframework/resources/languages/',
	            'pattern'  => 'fr/Zend_Validate.php',
	        ),
 		),
	),
	'ppitRoles' => array(
			'PpitDocument' => array(
			),
	),
	'ppitDocumentDependencies' => array(
	),
	'ppitCoreDependencies' => array(
			'document' => new \PpitDocument\Model\Document,
	),
	'ppitDocument' => array(
	),
	'document/index' => array(
			'title' => array('en_US' => 'P-PIT Document', 'fr_FR' => 'P-PIT Document'),
	),
);
