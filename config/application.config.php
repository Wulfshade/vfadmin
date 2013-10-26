<?php
$config = array(
    'modules' => array(
        'Application',
        'Fitments',
        'Vehicles',
        'Settings',
    ),

    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
        ),

        'config_glob_paths' => array(
            //'config/autoload/{,*.}{global,local}.php',
        ),
    ),
);

if(file_exists('module/vfsaas')) {
    array_push($config['modules'], 'vfsaas');
}

return $config;