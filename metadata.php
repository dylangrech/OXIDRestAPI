<?php

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id'          => 'fcrestapiproject',
    'title'       => [
        'de' => 'Rest API',
        'en' => 'Rest API',
    ],
    'description' => [
        'de' => 'x',
        'en' => 'x',
    ],
    'thumbnail'   => '',
    'version'     => '1.0',
    'author'      => 'Dylan Grech',
    'url'         => 'https://www.oxid-esales.com/',
    'email'       => 'dylangrech99@gmail.com',
    'controllers' => [
        'RestUserGroup' => Fatchip\RestAPI\Controller\RestUserGroup::class,
    ],
    'templates' => [
        'test_form.tpl' => 'fc\fcrestapiproject\views\test_form.tpl',
    ],
    'extend'      => [],
    'blocks'      => [
        ['template' => 'article_main.tpl', 'block' => 'admin_article_main_form', 'file' => 'fc_test_form.tpl'],
    ],
    'events'       => [],
    'settings'     => []
];
