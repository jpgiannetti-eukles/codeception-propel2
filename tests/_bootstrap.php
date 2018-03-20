<?php

use Propel\Generator\Util\QuickBuilder;

require __DIR__ . "/../vendor/autoload.php";

$builder = new QuickBuilder();
$builder->setSchema(<<<SCHEMA
<!--suppress ALL -->
<database name="default">
	<table name="user">
		<column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER"/>
		<column name="name" required="true" type="VARCHAR"/>		
	</table>
</database>
SCHEMA
);

$builder->build();
