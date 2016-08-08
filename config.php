<?php

$rootDir = dirname(__FILE__); 
require $rootDir . "/vendor/autoload.php";

use oat\search\helper\SupportedOperatorHelper as SupportedOperatorHelper;

$ServicConfig = new \Zend\ServiceManager\Config(
            array(
                'shared' => array(
                    'search.query.query' => false,
                    'search.query.builder' => false,
                    'search.query.criterion' => false,
                    'search.tao.serialyser' => false,
                    'search.tao.result' => false
                ),
                'invokables' => array(
                    'search.query.query' => '\\oat\\search\\Query',
                    'search.query.builder' => '\\oat\\search\\QueryBuilder',
                    'search.query.criterion' => '\\oat\\search\\QueryCriterion',
                    'search.driver.postgres' => '\\oat\\search\\DbSql\\Driver\\PostgreSQL',
                    'search.driver.mysql' => '\\oat\\search\\DbSql\\Driver\\MySQL',
                    'search.driver.tao' => '\\oat\\oatbox\\search\\driver\\TaoSearchDriver',
                    'search.tao.serialyser' => '\\oat\\search\\DbSql\\TaoRdf\\UnionQuerySerialyser',
                    'search.factory.query' => '\\oat\\search\\factory\\QueryFactory',
                    'search.factory.builder' => '\\oat\\search\\factory\\QueryBuilderFactory',
                    'search.factory.criterion' => '\\oat\\search\\factory\\QueryCriterionFactory',
                    'search.tao.gateway' => '\\oat\\search\\TaoSearchGateWay',
                    'search.tao.result' => '\\oat\\search\\TaoResultSet'
                ),
                'abstract_factories' => array(
                    '\\oat\\search\\Command\\OperatorAbstractfactory'
                ),
                'services' => array(
                    'search.options' => array(
                        'table'    => 'statements',
                        'driver'   => 'taoRdf',
                        'language' => 'en-US',
                    )
                )
            )
        );

$ServiceLocator = new \Zend\ServiceManager\ServiceManager($ServicConfig);


