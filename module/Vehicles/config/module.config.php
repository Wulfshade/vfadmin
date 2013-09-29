<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(

            'vehicles' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/vehicles',
                    'defaults' => array(
                        'controller' => 'Vehicles\Controller\Vehicles',
                        'action'     => 'index',
                    ),
                ),
            ),

            'vehicles-import' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/vehicles-import',
                    'defaults' => array(
                        'controller' => 'Vehicles\Controller\Import',
                        'action'     => 'index',
                    ),
                ),
            ),

            'vehicles-export' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/vehicles-export',
                    'defaults' => array(
                        'controller' => 'Vehicles\Controller\Export',
                        'action'     => 'index',
                    ),
                ),
            ),

        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Vehicles\Controller\Vehicles' => 'Vehicles\Controller\VehiclesController',
            'Vehicles\Controller\Import' => 'Vehicles\Controller\ImportController',
            'Vehicles\Controller\Export' => 'Vehicles\Controller\ExportController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
