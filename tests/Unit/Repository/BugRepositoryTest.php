<?php

namespace Tests\Unit\Repository;

use App\Repository\BugRepository;
use Doctrine\Persistence\ManagerRegistry;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Class BugRepositoryTest.
 *
 * @covers \App\Repository\BugRepository
 */
class BugRepositoryTest extends TestCase
{
    /**
     * @var BugRepository
     */
    protected $bugRepository;

    /**
     * @var ManagerRegistry|Mock
     */
    protected $registry;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = Mockery::mock(ManagerRegistry::class);
        $this->bugRepository = new BugRepository($this->registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->bugRepository);
        unset($this->registry);
    }
}
