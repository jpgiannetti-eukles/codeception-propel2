<?php

namespace Wollanup\Codeception\Module;

use Codeception\Lib\Interfaces\ActiveRecord;
use Codeception\Module;
use Codeception\TestInterface;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Connection\ConnectionInterface;
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
     * @param       $entity
     * @param array $data
     *
     * @return ActiveRecordInterface|null
     */
    public function grabRecord($entity, $data = [])
    {
        $query = $this->buildQuery($entity, $data);

        return $query->findOne($this->connection);
    }

    /**
     * @param string $entity
     * @param array  $data
     *
     * @return ActiveRecordInterface
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function haveRecord($entity, $data = [])
    {
        $query = $this->buildQuery($entity, $data);

        $activeRecord = $query->findOneOrCreate($this->connection);

        if ($activeRecord->isNew()) {
            $activeRecord->save();
        }

        return $activeRecord;
    }

    /**
     * @param string $entity
     * @param array  $data
     */
    public function seeRecord($entity, $data = [])
    {
        $record = $this->grabRecord($entity, $data);
        $this->assertNotNull($record);
    }

    /**
     * @param       $entity
     * @param array $data
     *
     * @return ModelCriteria
     */
    private function buildQuery($entity, $data = [])
    {
        $tableMap   = $entity::TABLE_MAP;
        $tableName  = $tableMap::TABLE_NAME;
        $queryClass = $entity . 'Query';

        /** @var ModelCriteria $query */
        $query = new $queryClass();
        foreach ($data as $property => $value) {
            $query->where("$tableName.$property" . ' = ?', $value);
        }

        return $query;
    }
}
