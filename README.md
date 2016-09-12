# lib-generis-search

This library provide an object search API.

It use a query builder to store search criteria, a query serialyser to transform builder 
into an exploitable query for your database driver and a gateway to execute query.

It Will return an iterator.

see API Documentation at http://forge.taotesting.com/projects/tao/wiki/use-complex-search-API

## usage example
```php
/* @var $search \oat\oatbox\search\ComplexeSearchService */
$search = $this->getServiceManager()->get(\oat\oatbox\search\ComplexeSearchService::SERVICE_ID);
/* @var $queryBuilder \oat\search\QueryBuilder */
$queryBuilder = $search->query();
/* search for all test takers */
$query = $search
       ->searchType($queryBuilder, 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject' , true)
             ->add('http://www.w3.org/2000/01/rdf-schema#label')
             ->contain('11');

$queryBuilder->setCriteria($query);
/* return an iterator */        
$result = $search->getGateway()->search($queryBuilder);
/* get max result */
echo 'total : ' . $result->total() . '<br><br>';
/*@var $row \core_kernel_classes_Resource */
foreach ($result as $row) {
    /* each iterator entry is a resource object */
    var_dump($row->getLabel());
    echo '<br>';
}
```
