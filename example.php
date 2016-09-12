<?php

include_once './config.php';
/*@var $GateWay \oat\search\base\SearchGateWayInterface */
$GateWay = $ServiceLocator->get('search.tao.gateway');
$GateWay->setServiceLocator($ServiceLocator)->init();
/*@var $queryBuilder \oat\search\base\QueryBuilderInterface */
$queryBuilder = $GateWay->query();
/**
 * "http://www.taotesting.com/movies.rdf#directedBy" => ("http://www.taotesting.com/movies.rdf#MartinScorsese", "http://www.taotesting.com/movies.rdf#QuentinTarantino")
 * "http://www.taotesting.com/movies.rdf#starring" => ("http://www.taotesting.com/movies.rdf#LeonardoDiCaprio", "http://www.taotesting.com/movies.rdf#ChristophWaltz")
 */
/*@var $query1 \oat\search\base\QueryInterface */
$query1 = $queryBuilder
        ->newQuery()
        ->add('http://www.taotesting.com/movies.rdf#directedBy')
        ->equals('http://www.taotesting.com/movies.rdf#MartinScorsese')
        ->addOr('http://www.taotesting.com/movies.rdf#QuentinTarantino')
        ->add('http://www.taotesting.com/movies.rdf#year')
        ->in('2012' , '2013' , '2014');

/*@var $query2 \oat\search\base\QueryInterface */
$query2 = $queryBuilder->newQuery()
        ->add('http://www.taotesting.com/movies.rdf#starring')
        ->equals('http://www.taotesting.com/movies.rdf#LeonardoDiCaprio')
        ->addOr('http://www.taotesting.com/movies.rdf#ChristophWaltz')
        ->add('http://www.taotesting.com/movies.rdf#year')
        ->in('2012' , '2013' , '2014');

/*@var $query3 \oat\search\base\QueryInterface */
$query3 = $queryBuilder->newQuery()
        ->add('http://www.taotesting.com/movies.rdf#year')
        ->between('2008' , '2010')
        ->add('http://www.taotesting.com/movies.rdf#directedBy')
        ->equals('http://www.taotesting.com/movies.rdf#MartinScorsese');

$queryBuilder->setCriteria($query1)
        ->setOr($query2)
        ->setOr($query3)
        ->setLimit(5)->sort(
            ['http://www.w3.org/2000/01/rdf-schema#label' => 'asc']
        );

echo $GateWay->getSerialyser()->pretty(true)->setCriteriaList($queryBuilder)->serialyse();
