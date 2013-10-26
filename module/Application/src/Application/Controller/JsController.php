<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

class JsController extends AbstractController
{
    protected $thisControllerRequiresLoginToView = false;

    function jsAction()
    {
        header('Access-Control-Allow-Origin: *');
        $processURL = $this->baseURL() . $this->url()->fromRoute('process') . '?';
        \VF_Singleton::getInstance()->setProcessURL($processURL);
        header('Content-Type:application/x-javascript');
        echo 'jQuery.noConflict();';
        require_once 'VF/html/vafAjax.js.include.php';
        exit;
    }

    function processAction()
    {
        header('Access-Control-Allow-Origin: *');
        require_once 'VF/html/vafAjax.include.php';
        exit;
    }
}
