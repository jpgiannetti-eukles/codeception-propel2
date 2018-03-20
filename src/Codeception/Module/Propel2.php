<?php

namespace Wollanup\Codeception\Module;

use Codeception\Lib\Interfaces\ActiveRecord;
use Codeception\Module;
use Codeception\TestInterface;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Propel;

/**
 * This module provides integration with [Propel2](http://propelorm.org) ORM
 *
 * @package Wollanup\Codeception\Module
 */
class Propel2 extends Module implements ActiveRecord
{

    /**
     * @var array
     */
    protected $config
        = [
            'cleanup'    => true,
            'connection' => null,
        ];
    /**
     *
     * @var ConnectionInterface $connection
     */
    protected $connection;

    /**
     * @param TestInterface $test
     */
    public function _after(TestInterface $test)
    {
        if ($this->config['cleanup'] && $this->connection->inTransaction()) {
            $this->connection->rollback();
        }
    }

    /**
     * @param TestInterface $test
     */
    public function _before(TestInterface $test)
    {
        if ($this->config['cleanup']) {
            $this->connection->beginTransaction();
        }
    }

    /**
     * @param array $settings
     */
    public function _beforeSuite($settings = [])
    {
        $this->connection = Propel::getConnection($this->config['connection']);
    }

    /**
     * @param string $entity
     * @param array  $data
     */
    public function dontSeeRecord($entity, $data = [])
    {
        $record = $this->grabRecord($entity, $data);
        $this->assertNull($record);
    }

    /**
     * @param        $entity
     * @param array  $data
     *
     * @param string $keyType
     *
     * @return ActiveRecordInterface|null
     */
    public function grabRecord($entity, $data = [], $keyType = TableMap::TYPE_PHPNAME)
    {
        $queryClass = $entity . 'Query';

        /** @var ModelCriteria $query */
        $query = new $queryClass;
        foreach ($data as $property => $value) {
            $filterByProperty = 'filterBy' . TableMap::translateFieldnameForClass($entity, $property, $keyType,
                    TableMap::TYPE_PHPNAME);
            $query->$filterByProperty($value);
        }

        return $query->findOne($this->connection);
    }

    /**
     * @param string $entity
     * @param array  $data
     *
     * @param string $keyType
     *
     * @return ActiveRecordInterface|object
     */
    public function haveRecord($entity, $data = [], $keyType = TableMap::TYPE_PHPNAME)
    {
        /** @var object $record */
        $record = new $entity;
        $record->fromArray($data);
        $record->save();

        return $record;
    }

    /**
     * @param string $entity
     * @param array  $data
     */
    public function seeRecord($entity, $data = [])
    {
        $record = $this->grabRecord($entity, $data);
        $this->assertInstanceOf(ActiveRecordInterface::class, $record);
    }

}
