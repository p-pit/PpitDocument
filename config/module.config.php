<?php

return array(
    'controllers' => array(
        'invokables' => array(
        	'PpitDocument\Controller\Public' => 'PpitDocument\Controller\PublicController',
        ),
    ),
 
    'router' => array(
        'routes' => array(
        	'public' => array(
                'type'    => 'literal',
                'options' => array(
                    'route'    => '/public',
                    'defaults' => array(
                        'controller' => 'PpitDocument\Controller\Public',
                        'action'     => 'displayPage',
                    ),
                ),
            	'may_terminate' => true,
            		'child_routes' => array(
            				'displayBlog' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/blog[/:directory][/:name]',
            								'constraints' => array(
											        'directory' => '[a-zA-Z0-9_-]+',
											        'name' => '[a-zA-Z0-9_-]+',
            								),
	        								'defaults' => array(
	        										'action' => 'displayBlog',
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
            				'communityHome' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/community-home',
	        								'defaults' => array(
	        										'action' => 'communityHome',
	        								),
	        						),
	        				),
            				'communityHomePrint' => array(
	        						'type' => 'segment',
	        						'options' => array(
	        								'route' => '/community-home-print',
	        								'defaults' => array(
	        										'action' => 'communityHomePrint',
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
				array('route' => 'public/displayContent', 'roles' => array('guest')),
				array('route' => 'public/displayPage', 'roles' => array('guest')),
				array('route' => 'public/displayBlog', 'roles' => array('guest')),
				array('route' => 'public/home', 'roles' => array('guest')),
				array('route' => 'public/communityHome', 'roles' => array('guest')),
				array('route' => 'public/communityHomePrint', 'roles' => array('guest')),
			)
		)
	),
		
    'view_manager' => array(
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
	'ppitDocumentDependencies' => array(
	),
);
