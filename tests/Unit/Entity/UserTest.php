<?php

namespace Tests\Unit\Entity;

use App\Entity\User;
use ReflectionClass;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest.
 *
 * @covers \App\Entity\User
 */
class UserTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->user);
    }

    public function testGetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(User::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getId());
    }

    public function testSetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(User::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $this->user->setId($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetEmail(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(User::class))
            ->getProperty('email');
        $property->setAccessible(true);
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getEmail());
    }

    public function testSetEmail(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(User::class))
            ->getProperty('email');
        $property->setAccessible(true);
        $this->user->setEmail($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetUserIdentifier(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetUsername(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetRoles(): void
    {
        $expected = [];
        $property = (new ReflectionClass(User::class))
            ->getProperty('roles');
        $property->setAccessible(true);
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getRoles());
    }

    public function testSetRoles(): void
    {
        $expected = [];
        $property = (new ReflectionClass(User::class))
            ->getProperty('roles');
        $property->setAccessible(true);
        $this->user->setRoles($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetPassword(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(User::class))
            ->getProperty('password');
        $property->setAccessible(true);
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getPassword());
    }

    public function testSetPassword(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(User::class))
            ->getProperty('password');
        $property->setAccessible(true);
        $this->user->setPassword($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetSalt(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testEraseCredentials(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetFirstName(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetFirstName(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetLastName(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetLastName(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetLevel(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetLevel(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetSpecialization(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetSpecialization(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetTechnology(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetTechnology(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetProjects(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testAddProject(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testRemoveProject(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetBugs(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
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

    public function testGetSubmittedBugs(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testAddSubmittedBug(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testRemoveSubmittedBug(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testGetIsConfirmed(): void
    {
        $expected = true;
        $property = (new ReflectionClass(User::class))
            ->getProperty('isConfirmed');
        $property->setAccessible(true);
        $property->setValue($this->user, $expected);
        $this->assertSame($expected, $this->user->getIsConfirmed());
    }

    public function testSetIsConfirmed(): void
    {
        $expected = true;
        $property = (new ReflectionClass(User::class))
            ->getProperty('isConfirmed');
        $property->setAccessible(true);
        $this->user->setIsConfirmed($expected);
        $this->assertSame($expected, $property->getValue($this->user));
    }

    public function testGetBirthdate(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }

    public function testSetBirthdate(): void
    {
        /** @todo This startPage is incomplete. */
        $this->markTestIncomplete();
    }
}
