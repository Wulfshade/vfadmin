<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    function bootstrap()
    {
        define( 'ELITE_CONFIG_DEFAULT', dirname(__FILE__).'/config.default.ini' );
        define( 'ELITE_CONFIG', dirname(__FILE__).'/config.ini' );
        define( 'ELITE_PATH', '.' );

        \VF_Singleton::getInstance()->setProcessURL('/modules/vaf/process.php?');
        $database = new \VF_TestDbAdapter(array(
            'dbname' => 'prestashop',
            'username' => 'prestashop',
            'password' => 'prestashop'
        ));
        \VF_Singleton::getInstance()->setReadAdapter($database);
    }

    public function indexAction()
    {
        $this->bootstrap();

        $schema = new \VF_Schema;

        if($this->getRequest()->isPost()) {
            $schemaGenerator = new \VF_Schema_Generator();
            $schemaGenerator->dropExistingTables();
            $schemaGenerator->execute(explode(",", $_POST['schema']));

            $this->flashMessenger()
                ->setNamespace('success')
                ->addMessage('Saved Schema');
        }

        return array(
            'schema' => $schema->getLevels()
        );
    }
}
