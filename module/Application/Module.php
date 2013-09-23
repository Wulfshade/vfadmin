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

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $this->bootstrap();
    }

    function bootstrap()
    {
        define( 'ELITE_CONFIG_DEFAULT', dirname(__FILE__).'/config.default.ini' );
        define( 'ELITE_CONFIG', dirname(__FILE__).'/config.ini' );
        define( 'ELITE_PATH', '.' );

        switch($this->whichShoppingCart()) {
            case 'prestashop':
                require_once $this->shoppingCartRoot().'/config/settings.inc.php';
                $database = new \VF_TestDbAdapter(array(
                    'dbname' => _DB_NAME_,
                    'username' => _DB_USER_,
                    'password' => _DB_PASSWD_
                ));
                break;
        }

        \VF_Singleton::getInstance()->setProcessURL('/modules/vaf/process.php?');
        \VF_Singleton::getInstance()->setReadAdapter($database);
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

    /**
     * Attempts to detect which shopping cart is in the document root (path immediately where VF admin panel is install)
     * Calculates path to scan based on SCRIPT_FILENAME env variable, so that symlinks are not resolved.
     * This means even if the admin panel is symlinked in, it finds the path where the symlink exists, not where the admin itself is.
     */
    public function whichShoppingCart()
    {
        $shoppingCartRoot = $this->shoppingCartRoot();

        $indexCode = file_get_contents($shoppingCartRoot.'index.php');

        if(preg_match('/PrestaShop/', $indexCode)) {
            return 'prestashop';
        }
    }

    // this returns the "simulated" (unresolved) path if run through a symlink
    public function shoppingCartRoot()
    {
        $script = $_SERVER["SCRIPT_FILENAME"];
        return str_replace('vfadmin/public', '', dirname($script));
    }

}