<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

class DashboardController extends AbstractController
{
    public function indexAction()
    {
        $this->layout('layout/layout-dashboard.phtml');

        $schema = $this->getServiceLocator()->get('vfschema');
        $vehicleCount = $this->finder()->countAll();

        return array(
            'schema' => $schema->getLevels(),
            'vehicleCount' => $vehicleCount,
            'shoppingCartName' => $this->shoppingCartEnvironment()->whichShoppingCart(),
        );
    }
}
