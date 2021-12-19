<?php

namespace Tests\Unit\Entity;

use App\Entity\Bug;
use App\Entity\User;
use Mockery;
use ReflectionClass;
use PHPUnit\Framework\TestCase;

/**
 * Class BugTest.
 *
 * @covers \App\Entity\Bug
 */
class BugTest extends TestCase
{
    /**
     * @var Bug
     */
    protected $bug;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->bug = new Bug();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->bug);
    }

    public function testGetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(Bug::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->bug, $expected);
        $this->assertSame($expected, $this->bug->getId());
    }

    public function testSetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(Bug::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $this->bug->setId($expected);
        $this->assertSame($expected, $property->getValue($this->bug));
    }

    public function testGetDescription(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetDescription(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetSeverity(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetSeverity(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetStatus(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetStatus(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetDate(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetDate(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetProject(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetProject(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetResponsibleUser(): void
    {
        $expected = Mockery::mock(User::class);
        $property = (new ReflectionClass(Bug::class))
            ->getProperty('responsibleUser');
        $property->setAccessible(true);
        $property->setValue($this->bug, $expected);
        $this->assertSame($expected, $this->bug->getResponsibleUser());
    }

    public function testSetResponsibleUser(): void
    {
        $expected = Mockery::mock(User::class);
        $property = (new ReflectionClass(Bug::class))
            ->getProperty('responsibleUser');
        $property->setAccessible(true);
        $this->bug->setResponsibleUser($expected);
        $this->assertSame($expected, $property->getValue($this->bug));
    }

    public function testGetSubmitter(): void
    {
        $expected = Mockery::mock(User::class);
        $property = (new ReflectionClass(Bug::class))
            ->getProperty('submitter');
        $property->setAccessible(true);
        $property->setValue($this->bug, $expected);
        $this->assertSame($expected, $this->bug->getSubmitter());
    }

    public function testSetSubmitter(): void
    {
        $expected = Mockery::mock(User::class);
        $property = (new ReflectionClass(Bug::class))
            ->getProperty('submitter');
        $property->setAccessible(true);
        $this->bug->setSubmitter($expected);
        $this->assertSame($expected, $property->getValue($this->bug));
    }
}
