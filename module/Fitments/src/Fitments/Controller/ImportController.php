<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Fitments\Controller;

use Application\Controller\AbstractController;

class ImportController extends AbstractController
{
    function indexAction()
    {
        $schema = new \VF_Schema;

        if($this->getRequest()->isPost()) {

            if($this->attemptedFileUpload() && $this->fileUploadHasError()) {
                $this->flashFileUploadErrorMessage();
            } else {

                if($_FILES['file']['tmp_name']) {
                    $tmpFile = $_FILES['file']['tmp_name'];
                } else {
                    $tmpFile = sys_get_temp_dir() . '/' . sha1(uniqid());
                    file_put_contents($tmpFile, $_POST['text']);
                }

                $shoppingCartEnvironment = $this->shoppingCartEnvironment();
                $dbInfo = $shoppingCartEnvironment->databaseDetails();

                $importer = new \VF_Import_ProductFitments_CSV_Import($tmpFile);
                $importer
                    ->setProductTable($dbInfo['product_table'])
                    ->setProductSkuField($dbInfo['product_sku_field'])
                    ->setProductIdField($dbInfo['product_id_field']);

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
