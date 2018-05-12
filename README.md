# CsvUtils

## Usage
```php
$arrayToCsv = new ArrayToCsvString;

$arrayToCsv->setIncludeBom(TRUE);
$arrayToCsv->setHeader([
	'Header 1',
	'Header 2',
	'Header 3'
]);
$arrayToCsv->addColumnCallback('price', [$this, 'formatFloat']);
$arrayToCsv->addColumnCallback('sales', [$this, 'formatFloat']);
$arrayToCsv->addColumnCallback('cost_of_sales', [$this, 'formatFloat']);

$yourCsvInString = $arrayToCsv->convert($rows);
```
