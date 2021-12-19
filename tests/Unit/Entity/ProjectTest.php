<?php

namespace Tests\Unit\Entity;

use App\Entity\Project;
use Doctrine\Common\Collections\Collection;
use Mockery;
use ReflectionClass;
use PHPUnit\Framework\TestCase;

/**
 * Class ProjectTest.
 *
 * @covers \App\Entity\Project
 */
class ProjectTest extends TestCase
{
    /**
     * @var Project
     */
    protected $project;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->project = new Project();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->project);
    }

    public function testGetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(Project::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->project, $expected);
        $this->assertSame($expected, $this->project->getId());
    }

    public function testSetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(Project::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $this->project->setId($expected);
        $this->assertSame($expected, $property->getValue($this->project));
    }

    public function testGetName(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetName(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetDeadline(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetDeadline(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetProgrammerCount(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetProgrammerCount(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetBugs(): void
    {
        $expected = Mockery::mock(Collection::class);
        $property = (new ReflectionClass(Project::class))
            ->getProperty('bugs');
        $property->setAccessible(true);
        $property->setValue($this->project, $expected);
        $this->assertSame($expected, $this->project->getBugs());
    }

    public function testAddBug(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testRemoveBug(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetUsers(): void
    {
        $expected = Mockery::mock(Collection::class);
        $property = (new ReflectionClass(Project::class))
            ->getProperty('users');
        $property->setAccessible(true);
        $property->setValue($this->project, $expected);
        $this->assertSame($expected, $this->project->getUsers());
    }

    public function testAddUser(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testRemoveUser(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }
}
