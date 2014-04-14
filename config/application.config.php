<?php

define("__BASE__", dirname(__DIR__) );
define("__PUBLIC__", __BASE__ . '/public');
define("__VENDOR__", __BASE__ . '/vendor');
define("__DATA__", __BASE__ . '/data');

return array(
    'modules' => array(
        'ZFTool',
        'ZendDeveloperTools',

        'AssetManager',

        'DoctrineTools',
        'DoctrineModule',
        'DoctrineORMModule',

// 		'DiWrapper',
//		'OcraServiceManager',
//    	'OcraDiCompiler',

        'ZfcTwig',
        'TwbBundle',    // Bootstrap3 integration

        'PhlyRestfully',

        'EcampLib',
        'EcampCore',
        'EcampWeb',
        'EcampApi',
        'EcampDB',
//     	'EcampDev',

        'EcampStoryboard',
        'EcampMaterial',

        'Application'
    ),

    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
            './plugins'
        ),
        'config_glob_paths' => array('config/autoload/{,*.}{global,local}.php')
    )
);
