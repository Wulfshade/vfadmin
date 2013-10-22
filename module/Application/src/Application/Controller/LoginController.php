<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

class LoginController extends AbstractController
{
    function indexAction()
    {
        if($this->getRequest()->isPost()) {
            $username_entered = $this->params()->fromPost('username');
            $password_entered = $this->params()->fromPost('password');
            if($this->isValid($username_entered, $password_entered)) {
                echo 'valid';exit;
            } else {
                echo 'invalid';exit;
            }
        }
    }

    function isValid($username_entered, $password_entered)
    {
        $whichCart = $this->shoppingCartEnvironment()->whichShoppingCart();
        switch($whichCart) {
            case 'magento':
                $password_string = $this->db()->select()
                    ->from('admin_user', array('password'))
                    ->where('username = ?', $username_entered)
                    ->query()
                    ->fetchColumn();
                if(false === $password_string) {
                    // no user found
                    return false;
                } else {
                    $password_string = explode(':', $password_string);
                    $hash = $password_string[0];
                    $salt = $password_string[1];

                    return md5($salt . $password_entered) === $hash;
                }
                break;
            case 'prestashop':
                require_once $this->shoppingCartEnvironment()->shoppingCartRoot() . '/config/settings.inc.php';
                $salt = _COOKIE_KEY_;
                $hash = md5($salt . $password_entered);
                $result = $this->db()->select()
                    ->from('ps_employee', array(new \Zend_Db_Expr('count(*)')))
                    ->where('email = ?', $username_entered)
                    ->where('passwd = ?', $hash)
                    ->query()
                    ->fetchColumn();
                return (bool)$result;
            break;
            default:
                throw new Exception($whichCart);
                break;
        }
    }


}