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
    protected $thisControllerRequiresLoginToView = false;

    function indexAction()
    {
        // give them a stripped down layout w/ nothing but the login form
        $this->layout()->setTemplate('layout/layout-login.phtml');

        // if already logged in, don't let them see the login form.
        if(isset($_SESSION['logged_in'])) {
            return $this->redirect()->toRoute('dashboard');
        }

        // validate what they entered
        if($this->getRequest()->isPost()) {
            $username_entered = $this->params()->fromPost('username');
            $password_entered = $this->params()->fromPost('password');
            if($this->isValid($username_entered, $password_entered)) {
                // set a flag logging them in, and send them to their dashboard
                $_SESSION['logged_in'] = 1;
                $this->flashMessenger()->addSuccessMessage('Welcome, ' . $username_entered);
                return $this->redirect()->toRoute('dashboard');
            } else {
                // flash a message explaining they typed the username or password wrong
                $this->flashMessenger()->addErrorMessage('Those credentials weren\'t valid.');
            }
        }

        return(array(
            'username_entered' => $this->params()->fromPost('username'),
        ));
    }

    function logoutAction()
    {
        unset($_SESSION['logged_in']);
        return $this->redirect()->toRoute('login');
    }

    /**
     * Authenticate credentials against the correct shopping cart's password table
     * @param $username_entered
     * @param $password_entered
     * @return bool
     * @throws \Exception
     */
    function isValid($username_entered, $password_entered)
    {
        $whichCart = $this->shoppingCartEnvironment()->whichShoppingCart();
        switch($whichCart) {
            case 'magento':
                return $this->isValidMagento($username_entered, $password_entered);
            case 'prestashop':
                return $this->isValidPrestashop($username_entered, $password_entered);
            break;
            default:
                throw new \Exception($whichCart . ' is not recognized as a cart.');
        }
    }

    /**
     * Authenticate credentials against Magento's admin_user table
     * @param $username_entered
     * @param $password_entered
     * @return bool
     */
    function isValidMagento($username_entered, $password_entered)
    {
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
    }

    /**
     * Authenticate credentials against Prestashop's ps_employee table
     * @param $username_entered
     * @param $password_entered
     * @return bool
     */
    function isValidPrestashop($username_entered, $password_entered)
    {
        // the salt used to hash the PW in the DB is stored in the settings file, its a constant.
        require_once $this->shoppingCartEnvironment()->shoppingCartRoot() . '/config/settings.inc.php';
        $salt = _COOKIE_KEY_;
        // if the password is correct, it will be this:
        $hash = md5($salt . $password_entered);
        // check if that is the one stored
        $result = $this->db()->select()
            ->from('ps_employee', array(new \Zend_Db_Expr('count(*)')))
            ->where('email = ?', $username_entered)
            ->where('passwd = ?', $hash)
            ->query()
            ->fetchColumn();
        return (bool)$result;
    }

}