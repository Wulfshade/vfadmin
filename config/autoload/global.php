<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
    'service_manager' => array(
        'factories' => array(
            'database' => function ($serviceManager) {
                $shoppingCartEnvironment = new \Application\ShoppingCartEnvironment();
                $dbinfo = $shoppingCartEnvironment->databaseDetails();
                $database = new \VF_TestDbAdapter(array(
                    'dbname' => $dbinfo['dbname'],
                    'username' => $dbinfo['username'],
                    'password' => $dbinfo['password']
                ));
                return $database;
            },
        )
    )
);
