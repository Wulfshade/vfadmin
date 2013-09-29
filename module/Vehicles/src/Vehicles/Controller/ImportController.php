<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Vehicles\Controller;

use Application\Controller\AbstractController;

class ImportController extends AbstractController
{
    function indexAction()
    {
        $schema = new \VF_Schema;

        if($this->getRequest()->isPost()) {

            if($_FILES['file']['error']) {
                $this->flashFileUploadErrorMessage();
            } else {

                if($_FILES['file']['tmp_name']) {
                    $tmpFile = $_FILES['file']['tmp_name'];
                } else {
                    $tmpFile = sys_get_temp_dir() . '/' . sha1(uniqid());
                    file_put_contents($tmpFile, $_POST['text']);
                }

                $importer = new \VF_Import_VehiclesList_CSV_Import($tmpFile);
                $importer
                    ->setProductTable('ps_product')
                    ->setProductSkuField('reference')
                    ->setProductIdField('id_product');

                //$importer->setLog($log);
                $importer->import();

                $this->flashMessenger()
                    ->setNamespace('success')
                    ->addMessage('Imported Fitments');
            }
        }

        return array(
            'schema' => $schema->getLevels()
        );
    }

}