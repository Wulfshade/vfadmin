<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Fitments\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class ImportController extends AbstractActionController
{
    function indexAction()
    {
        $schema = new \VF_Schema;

        if($this->getRequest()->isPost()) {

            if($_FILES['file']['error']) {
                $this->flashMessenger()
                    ->setNamespace('error')
                    ->addMessage($this->codeToMessage($_FILES['file']['error']));
            } else {

                if($_FILES['file']['tmp_name']) {
                    $tmpFile = $_FILES['file']['tmp_name'];
                } else {
                    $tmpFile = sys_get_temp_dir() . '/' . sha1(uniqid());
                    file_put_contents($tmpFile, $_POST['text']);
                }

                $importer = new \VF_Import_ProductFitments_CSV_Import($tmpFile);
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

    function codeToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }
}
