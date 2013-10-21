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

class VehiclesController extends AbstractController
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

    function saveAction()
    {
        $dataToSave = $this->requestLevels();
        $vehiclesFinder = new \VF_Vehicle_Finder(new \VF_Schema());
        $vehicle = $vehiclesFinder->findOneByLevelIds($dataToSave, \VF_Vehicle_Finder::INCLUDE_PARTIALS);
        if($vehicle){
            $dataToSave = $vehicle->toTitleArray();
        } else {
            $dataToSave = array();
        }

        $dataToSave[$this->params()->fromQuery('entity')] = $this->params()->fromQuery('title');
        $vehicle = \VF_Vehicle::create(new \VF_Schema(), $dataToSave);
        $vehicle->save();

        if (1||$this->getRequest()->isXmlHttpRequest()) {
            echo $vehicle->getValue($this->params()->fromQuery('entity'));
            exit();
        }
    }

    function requestLevels()
    {
        $params = array();
        foreach($this->schema()->getLevels() as $level)
        {
            if($this->params()->fromQuery($level))
            {
                $params[$level] = $this->params()->fromQuery($level);
            }
        }
        return $params;
    }
}
