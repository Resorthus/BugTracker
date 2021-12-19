<?php

namespace Tests\Unit\Controller;

use App\Controller\BugController;
use Tests\TestCase;

/**
 * Class BugControllerTest.
 *
 * @covers \App\Controller\BugController
 */
class BugControllerTest extends TestCase
{
    /**
     * @var BugController
     */
    protected $bugController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->bugController = new BugController();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->bugController);
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
