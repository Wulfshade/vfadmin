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
use Zend\View\Model\ViewModel;

class IndexController extends AbstractController
{
    function indexAction()
    {
        $sku = $this->params()->fromQuery('sku');
        $results = $this->findProductsSkuLike($sku);

        if(strlen($sku) > 0 && 0 == count($results)) {
            $this->flashMessenger()->addInfoMessage('No products are found that match that SKU');
        }

        return array(
            'sku' => $sku,
            'results' => $results
        );
    }

    function findProductsSkuLike($sku, $limit=10)
    {
        $whichCart = $this->shoppingCartEnvironment()->whichShoppingCart();
        switch($whichCart) {
            case 'magento':
                return $this->findProductsSkuLike_magento($sku, $limit);
            case 'prestashop':
                return $this->findProductsSkuLike_prestashop($sku, $limit);
            default:
                throw new Exception('Unknown shopping cart ' . $whichCart);
        }
    }

    function findProductsSkuLike_magento($sku, $limit=10)
    {
        $result = $this->db()->select()
            ->from('catalog_product_entity', array('id' => 'entity_id', 'sku'))
            ->where('sku LIKE ?', '%' . $sku . '%')
            ->limit($limit)
            ->query()
            ->fetchAll();
        return $result;
    }

    function findProductsSkuLike_prestashop($sku, $limit=10)
    {
        $result = $this->db()->select()
            ->from('ps_product', array('id' => 'id_product', 'sku' => 'reference'))
            ->where('reference LIKE ?', '%' . $sku . '%')
            ->limit($limit)
            ->query()
            ->fetchAll();
        return $result;
    }

    function productmanageAction()
    {
        $schema = new \VF_Schema;
        $product = new \VF_Product();

        $product->setId($this->params()->fromRoute('id'));

        if($this->getRequest()->isPost()) {
            $this->removeFitments($product);
            $this->updateUniversal($product);
            //$this->updateRelated($product);
            $this->addNewFitments($product);
            $this->flashMessenger()->addSuccessMessage('Produt\'s fitments saved');
        }

        $view = new ViewModel(array(
            'fitments' => $product->getFits(),
            'product' => $product,
            'schema' => $schema->getLevels()
        ));
        $view->setTemplate('fitments/index/multitree.phtml');
        return $view;
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
