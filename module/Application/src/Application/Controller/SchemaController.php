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

class SchemaController extends AbstractActionController
{
    public function indexAction()
    {
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
