<?php

namespace Tests;

use Codeception\Test\Unit;
use Propel\Runtime\Connection\ConnectionWrapper;

class Propel2Test extends Unit
{

    const /** @noinspection PhpUndefinedClassInspection */
        USER_CLASS = \User::class;
    /**
     * @var ConnectionWrapper
     */
    protected $connection;
    /**
     * @var \Tests\UnitTester
     */
    protected $tester;

    public function testDontSeeRecord()
    {

        $this->tester->dontSeeRecord(self::USER_CLASS, ['Name' => 'Stuart']);
    }

    public function testGrabRecord()
    {
        $this->tester->haveRecord(self::USER_CLASS, ['Name' => "Kevin"]);
        $user = $this->tester->grabRecord(self::USER_CLASS, ['Name' => "Kevin"]);
        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertSame("Kevin", $user->getName());
    }

    public function testHaveRecord()
    {
        $record = $this->tester->haveRecord(self::USER_CLASS, ['Name' => "Bob"]);
        $this->assertFalse($record->isNew());
    }

    public function testSeeRecord()
    {
        $this->tester->haveRecord(self::USER_CLASS, ['Name' => "Bob"]);
        $this->tester->seeRecord(self::USER_CLASS, ['Name' => "Bob"]);
    }
}
