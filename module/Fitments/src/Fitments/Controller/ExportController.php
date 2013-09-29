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

class ExportController extends AbstractController
{
    function indexAction()
    {
        if (isset($_GET['go']))
        {
            $stream = fopen("php://output", 'w');
            $exporter = new \VF_Import_ProductFitments_CSV_Export();

            $shoppingCartEnvironment = $this->shoppingCartEnvironment();
            $dbInfo = $shoppingCartEnvironment->databaseDetails();

            $exporter
                ->setProductTable($dbInfo['product_table']);

            header(sprintf('Content-Disposition: attachment;filename="vaf-export-%s.csv"', time()));
            header('Content-Type: text/csv');
            $exporter->export($stream);

            exit();
        }
    }
}
