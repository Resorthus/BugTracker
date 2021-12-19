<?php

namespace Tests\Unit\Controller;

use App\Controller\ModifiedBugController;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManager;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Class ModifiedBugControllerTest.
 *
 * @covers \App\Controller\ModifiedBugController
 */
class ModifiedBugControllerTest extends TestCase
{
    /**
     * @var ModifiedBugController
     */
    protected $modifiedBugController;

    /**
     * @var ProjectRepository|Mock
     */
    protected $projectRepository;

    /**
     * @var EntityManager|Mock
     */
    protected $entityManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->projectRepository = Mockery::mock(ProjectRepository::class);
        $this->entityManager = Mockery::mock(EntityManager::class);
        $this->modifiedBugController = new ModifiedBugController($this->projectRepository, $this->entityManager);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->modifiedBugController);
        unset($this->projectRepository);
        unset($this->entityManager);
    }

    public function testGetBugs(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testMarkAsFixed(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testAddBug(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetBug(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testUpdateBug(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testDeleteBug(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }
}
