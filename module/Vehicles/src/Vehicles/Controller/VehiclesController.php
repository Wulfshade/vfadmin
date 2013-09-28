<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Vehicles\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class VehiclesController extends AbstractActionController
{
    public function indexAction()
    {
        $filter = $this->filter();

        $page = $this->params()->fromQuery('page',1);
        $perpage = 10;
        $offset = ($page * $perpage)-$perpage;

        $vehicles = $this->finder()->findByLevels($filter, false, $perpage, $offset);
        $vehicleCount = count($this->finder()->findByLevels($filter));

        $pageAdapter = new \Zend\Paginator\Adapter\Null($vehicleCount);
        $paginator = new \Zend\Paginator\Paginator($pageAdapter);
        $paginator->setItemCountPerPage($perpage);
        $paginator->setCurrentPageNumber($page);

        if($offset+$perpage > $vehicleCount) {
            $end = $vehicleCount;
        } else {
            $end = $offset+$perpage;
        }

        return array(
            'vehicles' => $vehicles,
            'schema' => $this->schema(),
            'start'=>$offset+1,
            'end'=>$end,
            'total'=>$vehicleCount,
            'paginator'=>$paginator,
        );
    }

    function finder()
    {
        $finder = new \VF_Vehicle_Finder($this->schema());
        return $finder;
    }

    function filter()
    {
        $filter = array();
        foreach($this->schema()->getLevels() as $level) {
            $value = $this->params()->fromQuery($level);
            if($value) {
                $filter[$level] = $value;
            }
        }
        return $filter;
    }

    function schema()
    {
        $schema = new \VF_Schema;
        return $schema;
    }
}
