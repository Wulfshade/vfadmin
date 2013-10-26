<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Settings\Controller;

use Application\Controller\AbstractController;
use Zend\View\Model\ViewModel;

class SchemaController extends AbstractController
{
    public function indexAction()
    {
        try {
            $schema = new \VF_Schema;
            $levels = $schema->getLevels();
        } catch( \Zend_Db_Statement_Exception $e ) {
            $levels = array();
        }

        if($this->getRequest()->isPost()) {
            $schemaGenerator = new \VF_Schema_Generator();
            $schemaGenerator->dropExistingTables();
            $schemaGenerator->execute(explode(",", $_POST['schema']));

            $this->flashMessenger()
                ->setNamespace('success')
                ->addMessage('Saved Schema');
        }

        return array(
            'schema' => $levels
        );
    }
}
