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

            'fitments' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/fitments-import',
                    'defaults' => array(
                        'controller' => 'Fitments\Controller\Import',
                        'action'     => 'index',
                    ),
                ),
            ),

            'fitments-import' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/fitments-import',
                    'defaults' => array(
                        'controller' => 'Fitments\Controller\Import',
                        'action'     => 'index',
                    ),
                ),
            ),

            'fitments-export' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/fitments-export',
                    'defaults' => array(
                        'controller' => 'Fitments\Controller\Export',
                        'action'     => 'index',
                    ),
                ),
            ),

        ),
    ),

    'controllers' => array(
        'invokables' => array(
            'Fitments\Controller\Import' => 'Fitments\Controller\ImportController',
            'Fitments\Controller\Export' => 'Fitments\Controller\ExportController',
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
