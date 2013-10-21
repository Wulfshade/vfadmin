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

class IndexController extends AbstractController
{
    function indexAction()
    {
        $schema = new \VF_Schema;
        $product = new \VF_Product();
        $product->setId(1);

        if($this->getRequest()->isPost()) {
            $this->removeFitments($product);
            $this->updateUniversal($product);
            //$this->updateRelated($product);
            $this->addNewFitments($product);
        }

        return array(
            'fitments' => $product->getFits(),
            'product' => $product,
            'schema' => $schema->getLevels()
        );
    }

    function removeFitments($product)
    {
        $schema = new \VF_Schema();
        if( is_array( $this->params()->fromPost( 'vaf-delete' ) ) && count( $this->params()->fromPost( 'vaf-delete' ) ) >= 1 )
        {
            foreach( $this->params()->fromPost( 'vaf-delete', array() ) as $fit )
            {
                $fit = explode( '-', $fit );
                $level = $fit[0];
                $fit = $fit[1];
                if( $level == $schema->getLeafLevel() )
                {
                    $product->deleteVafFit( $fit );
                }
            }
        }
    }

    function updateUniversal($product)
    {
        if( isset( $_POST['universal'] ) && $_POST['universal'] )
        {
            $product->setUniversal( true );
        }
        else
        {
            $product->setUniversal( false );
        }
    }

//    function updateRelated($product)
//    {
//        if (file_exists(ELITE_PATH . '/Vafrelated'))
//        {
//            $relatedProduct = new Elite_Vafrelated_Model_Catalog_Product($product);
//            if( isset( $_POST['related'] ) && $_POST['related'] )
//            {
//                $relatedProduct->setShowInRelated( true );
//            }
//            else
//            {
//                $relatedProduct->setShowInRelated( false );
//            }
//        }
//    }

    function addNewFitments($product)
    {
        if( is_array( $this->params()->fromPost( 'vaf' ) ) && count( $this->params()->fromPost( 'vaf' ) ) >= 1 )
        {
            foreach( $this->params()->fromPost( 'vaf' ) as $fit )
            {
                if( strpos($fit,':') && strpos($fit,';') )
                {
                    // new logic
                    $params = explode(';', $fit);
                    $newParams = array();
                    foreach($params as $key => $value)
                    {
                        $data = explode(':', $value);
                        if(count($data)<=1) continue;

                        $newParams[$data[0]] = $data[1];
                    }
                    $product->addVafFit($newParams);
                }
                else
                {
                    //legacy logic

                    $fit = explode( '-', $fit );
                    $level = $fit[0];
                    $fit = $fit[1];
                    $product->addVafFit( array( $level=> $fit ) );
                }
            }
        }
    }
}
