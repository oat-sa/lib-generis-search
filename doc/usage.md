# lib-generis-search

## service basic usage :

### criteria basic search :

search every items with label equal to 'foo'.

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);
$queryBuilder = $search->query()
$myquery = $queryBuilder->newQuery()->add(RDFS_LABEL)->equals('foo');
$queryBuilder->setCriteria($myquery);
$result = $search->getGateway()->search($queryBuilder);
```

### search by type :

search every test-takers.

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);
$queryBuilder = $search->query();
$query = $search->searchType( $queryBuilder , 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject' , true);
$queryBuilder->setCriteria($query);
$result = $search->getGateway()->search($queryBuilder);
```

add criteria : search every test-takers having label containing 'foo'.

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);
$queryBuilder = $search->query();
$query = $search->searchType($queryBuilder , 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject' , true)
                 ->add(RDFS_LABEL)
                 ->contains('foo');
$queryBuilder->setCriteria($query);
$result = $search->getGateway()->search($queryBuilder);
```

### language search :

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);
$queryBuilder = $search->query();
$query = $search->setLanguage($queryBuilder , $userLanguage , $defaultLanguage)
                 ->add(RDFS_LABEL)
                 ->contains('foo');
$queryBuilder->setCriteria($query);
$result = $search->getGateway()->search($queryBuilder);
```

### multiple criteria search :

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);
$queryBuilder = $search->query();
$query = $queryBuilder->newQuery()
                 ->add(RDFS_LABEL)
                 ->contains('foo')
                 ->add(RDFS_COMMENT)
                 ->contains('bar');

$queryBuilder->setCriteria($query);
$result = $search->getGateway()->search($queryBuilder);
```

### multiple values on same criterion search :

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);
$queryBuilder = $search->query();
$query = $queryBuilder->newQuery()
                 ->add(RDFS_LABEL)
                 ->contains('foo')
                 ->addOr('bar');
$queryBuilder->setCriteria($query);
$result = $search->getGateway()->search($queryBuilder);
```

using different operator  on same criterion :

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);
$queryBuilder = $search->query();
$query = $queryBuilder->newQuery()
                 ->add(RDFS_LABEL)
                 ->contains('foo')
                 ->addAnd('bar' , SupportedOperatorHelper::BEGIN);
$queryBuilder->setCriteria($query);
$result = $search->getGateway()->search($queryBuilder);
```

### use OR :

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);

$queryBuilder = $search->query();

$query = $queryBuilder->newQuery()
                 ->add(RDFS_LABEL)
                 ->begin('a')

$queryOr = $queryBuilder->newQuery()
                 ->add(RDFS_LABEL)
                 ->begin('z');

$queryBuilder->setCriteria($query)->setOr($queryOr);
$result = $search->getGateway()->search($queryBuilder);
```

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);

$queryBuilder = $search->query();

/**
* search for anything with a label begins by a AND comment contains "foo" and comment contains "bar" 
* OR
* anything with a label begins by b AND comment contains "titi" and comment contains "toto" 
*
**/
$queryBuilder = $search->query();

$query = $queryBuilder->newQuery()
                 ->add(RDFS_LABEL)->begin('a')
                 ->add(RDFS_COMMENT)->contains('foo')  
                 ->add(RDFS_COMMENT)->contains('bar')          

$queryOr = $queryBuilder->newQuery()
                 ->add(RDFS_LABEL)->begin('z')
                 ->add(RDFS_COMMENT)->contains('titi')  
                 ->add(RDFS_COMMENT)->contains('toto');

$queryBuilder->setCriteria($query)->setOr($queryOr);
$result = $search->getGateway()->search($queryBuilder);
```

by default, Criteria are grouped by AND operator :

to search test takers which label begin by 'a' or begin by 'z' :

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);

/**
* a test taker with label begins by 'a'
*
* or
*
* a test taker with comment begins by 'b'
**/

$queryBuilder = $search->query();

$query = $search->searchType($queryBuilder ,'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject' , true)
                 ->add(RDFS_LABEL)
                 ->begin('a');

$queryOr = $search->searchType( $queryBuilder ,'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject' , true)
                 ->add(RDFS_COMMENT)
                 ->begin('z');

$queryBuilder->setCriteria($query)->setOr($queryOr);

$result = $search->getGateway()->search($queryBuilder);
```

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);

/**
* a test taker with label begin by 'a' or label begin by 'b'
**/

$queryBuilder = $search->query();

$query = $search->searchType($queryBuilder ,'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject' , true)
                 ->add(RDFS_LABEL)
                 ->begin('a')
                 ->addOr('b');

$queryBuilder->setCriteria($query);

$result = $search->getGateway()->search($queryBuilder);
```

## supported operators :

### operators list :

| Constant | string value | SQL operator | Comment |
| -------- |------------- | ------------ | ------- |
| EQUAL | 'equals' | '=' | | 
| DIFFERENT | 'notEquals' | '!=' | | 
| GREATER_THAN | 'gt' | '>' | | 
| GREATER_THAN_EQUAL | 'gte' | '>=' | | 
| LESSER_THAN | 'lt' | '<' | | 
| LESSER_THAN_EQUAL | 'lte' | '<=' | | 
| BETWEEN | 'between' | BETWEEN 'value1' AND 'value2 | value must be an array with two indexes | 
| IN | 'in' | IN ('1' ,'3' , '5' ) | value must be an array Or a query builder  | 
| NOT_IN | 'notIn' | NOT IN ('1' ,'3' , '5' ) | value must be an array Or a query builder | 
| MATCH | 'match' | LIKE 'value' |  | 
| NOT_MATCH | 'notMatch' | NOT LIKE 'value' |  | 
| CONTAIN | 'contains' | LIKE '%value%' | | 
| BEGIN_BY | 'begin' | LIKE 'value%' | | 
| END_BY | 'end' | LIKE '%value' | | 
| IS_NULL | 'null' | IS NULL | set up value is ignored | 
| IS_NOT_NULL | 'notNull' | IS NOT NULL | set up value is ignored | 

use oat\search\helper\SupportedOperatorHelper to see all supported operators

## usage examples :

### simple value example :

```php
$query->add(RDFS_LABEL)->equals('foo');
$query->add(RDFS_LABEL)->gte(1);
```

OR :

```php
$query->addCriterion(RDFS_LABEL , SupportedOperatorHelper::EQUAL , 'foo');
$query)->addCriterion(RDFS_LABEL , SupportedOperatorHelper::GREATER_THAN_EQUAL , 1);
```

### between value example :

```php
$query->add(RDFS_LABEL)->between(1 , 10);
```

OR :

```php
$query->add(RDFS_LABEL)->between([1 , 10]);
```

OR :

```php
$query->addCriterion(RDFS_LABEL,SupportedOperatorHelper::BETWEEN , [1 , 10]);
```

### IN value example :

```php
$query->add(RDFS_LABEL)->in(1 , 5 , 10);
```

OR

```php
$query->add(RDFS_LABEL)->in([1 , 5 , 10]);
```

OR

```php
$query->addCriterion( RDFS_LABEL ,SupportedOperatorHelper::IN , [1 , 5 , 10]);
```

### NULL and NOT NULL value example :

```php
$query->add(RDFS_LABEL)->null(NULL);
$query->add(RDFS_LABEL)->notNull(NULL);
```

OR

```php
$query->addCriterion( RDFS_LABEL ,SupportedOperatorHelper::NULL , null);
$query->addCriterion( RDFS_LABEL ,SupportedOperatorHelper::NOT_NULL , null]);
```

## sorting Query :

### Sort method is available on QueryBuilder :

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);
$queryBuilder = $search->query()->sort(
[
RDFS_LABEL => 'asc'
]
);

$query = $queryBuilder->add(RDFS_COMMENT)->contains('foo');
$queryBuilder->setCriteria($query);

$result = $search->getGateway()->search($queryBuilder);
```

### Example for muliple sort :

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);

$queryBuilder = $search->query();

$query = $queryBuilder->add(RDFS_COMMENT)->contains('foo');

$queryBuilder->setCriteria($query)->sort(
[
RDFS_LABEL => 'asc',
RDFS_COMMENT => 'desc',
]
);
$result = $search->getGateway()->search($queryBuilder);
```

### random sorting

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);

$queryBuilder = $search->query();

$query = $queryBuilder->add(RDFS_COMMENT)->contains('foo');

$queryBuilder->setCriteria($query)->setRandom();

//fields are ignore when random is enable

$result = $search->getGateway()->search($queryBuilder);
```

## Limit and Offset :

limit method is also available on QueryBuilder :

get 10 results :

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);

$query = $queryBuilder->newQuery()->add(RDFS_COMMENT)->contains('foo');

$queryBuilder->setCriteria($query)->setLimit(10);

$result = $search->getGateway()->search($queryBuilder);
```
get 10 results offset 5 :

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);
$queryBuilder = $search->query()->setLimit(10)->setOffset(5);

$query = $queryBuilder->newQuery()->add(RDFS_COMMENT)->contains('foo');

$queryBuilder->setCriteria($query);

$result = $search->getGateway()->search($queryBuilder);
```

#### NB : offset without limit is ignored

## Gateway :

gateway is the highter component of complex search.
It provide query builder and it execute query using default database driver.

### only get number of result :

to get query number of result use count method :

```php
$total = $search->getGateway()->count($queryBuilder);
```

### debugging :

to debug query use printQuery method :

```php
$search->getGateway()->serialyse($queryBuilder);
$search->getGateway()->printQuery();
```

## Result Set :

a result set is returned by gateWay search method's.
It's an arrayIterator adding total method which return full number for your query .

Each entry of result set is a tao resource Object.

### basic usage :

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);

$queryBuilder = $search->query();
$query = $queryBuilder->newQuery()->add(RDFS_COMMENT)->contains('foo');

$queryBuilder->setCriteria($query);
$result = $search->getGateway()->search($queryBuilder)

foreach($result as $resource) {
       echo $resource->getLabel();
}
```

### use total :

```php
$search = $this->getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\searchComplexSearchService::SERVICE_ID);
$queryBuilder = $search->query();
$query = $queryBuilder->newQuery()->add(RDFS_COMMENT)->contains('foo');

$queryBuilder->setCriteria($query);
$result = $search->getGateway()->search($queryBuilder);

echo $result->total(); 
echo $result->count();

// 18
// 18
```
