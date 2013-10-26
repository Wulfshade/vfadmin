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

class ExportController extends AbstractController
{
    function indexAction()
    {
        $schema = $this->schema();

        if (isset($_GET['go']))
        {
            if ('CSV' == $_REQUEST['format'])
            {
                header(sprintf('Content-Disposition: attachment;filename="vaf-export-%s.csv"', time()));
                header('Content-Type: text/csv');
            } else
            {
                header(sprintf('Content-Disposition: attachment;filename="vaf-export-%s.xml"', time()));
                header('Content-Type: text/xml');
            }

            if ('CSV' == $_REQUEST['format'])
            {
                $stream = fopen("php://output", 'w');
                $exporter = new \VF_Import_VehiclesList_CSV_Export();
                $exporter->export($stream);
            } else
            {
                $exporter = new \VF_Import_VehiclesList_XML_Export();
                echo $exporter->export();
            }

            exit();
        }

        return array(
            'schema' => $schema->getLevels()
        );
    }

}