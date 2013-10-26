<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $app->getEventManager()->attach('dispatch', array($this, 'setLayout'));
        $this->bootstrap($app->getServiceManager());
    }

    function setLayout($e)
    {
        $controller = $e->getTarget();
        $controllerClass = get_class($controller);
        $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $controller->layout()->controller = $controllerClass;
        $controller->layout()->module = $moduleNamespace;
        $controller->layout()->route =  $route = $e->getRouteMatch()->getMatchedRouteName();
    }

    function bootstrap($sm)
    {
        define( 'ELITE_CONFIG_DEFAULT', dirname(__FILE__).'/config.default.ini' );
        define( 'ELITE_CONFIG', dirname(__FILE__).'/config.ini' );
        define( 'ELITE_PATH', '.' );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'database' => function ($serviceManager) {
                    $shoppingCartEnvironment = $serviceManager->get('shopping_cart_adapter');
                    if(false === $shoppingCartEnvironment->whichShoppingCart()) {
                        $database = new \VF_TestDbAdapter(array(
                            'dbname' => 'vfcore',
                            'username' => 'root',
                            'password' => ''
                        ));
                        return $database;
                    } else {
                        $dbinfo = $shoppingCartEnvironment->databaseDetails();
                        $database = new \VF_TestDbAdapter(array(
                            'dbname' => $dbinfo['dbname'],
                            'username' => $dbinfo['username'],
                            'password' => $dbinfo['password']
                        ));
                        return $database;
                    }
                },
                'shopping_cart_adapter' => function($serviceManager) {
                    $shoppingCartEnvironment = new \Application\ShoppingCartAdapter();
                    return new $shoppingCartEnvironment;
                },
                'vfsingleton' => function($sm) {
                    \VF_Singleton::getInstance()->setReadAdapter($sm->get('database'));
                    return \VF_Singleton::getInstance();
                },
                'vfschema' => function($sm) {
                    $sm->get('vfsingleton');
                    $schema = new \VF_Schema;
                    return $schema;
                }
            )
        );
    }

}