<?php

namespace App\Tests\Controller;

use App\Controller\ModifiedBugController;
use App\Entity\Bug;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModifiedBugControllerTest extends KernelTestCase
{
    private $entityManager;
    private $projectRepository;
    private $bugController;
    private $user;

    protected function setUp(): void
    {
        $bug1 = new Bug();
        $bug1->setDescription("Bad routing");
        $bug1->setStatus("Open");
        $bug1->setSeverity("High");
        $bug1->setId(1);
        $bug2 = new Bug();

        $project = new Project();
        $project->setId(1);
        $this->user = new User();

        $this->user->setRoles(['ROLE_PROGRAMMER']);
        $this->user->setIsConfirmed(true);
        $this->user->setId(1);
        $this->user->addProject($project);
        $bug1->setProject($project);
        $bug2->setProject($project);
        $this->user->addBug($bug1);
        $this->user->addBug($bug2);
        $this->user->addSubmittedBug($bug1);
        $this->user->addSubmittedBug($bug2);
        $project->addUser($this->user);

        $this->projectRepository = $this->createMock(ProjectRepository::class);
        $this->projectRepository->method('find')->will(
            $this->returnCallback(function ($arg) use ($project) {
                if ($arg == 1) {
                    return $project;
                } else {
                    return null;
                }
            })
        );

        $this->entityManager = $this->createStub(EntityManager::class);

        $this->bugController = new ModifiedBugController($this->projectRepository, $this->entityManager);

    }


    public function testGetBugSuccess()
    {
        $response = $this->bugController->GetBug(1, 1, $this->user, 1);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Bad routing", $responseBody['description']);
        $this->assertEquals("High", $responseBody['severity']);
        $this->assertEquals("Open", $responseBody['status']);
    }

    public static function provideUserDataToViewBug()
    {
        $user1 = new User();

        $user2 = new User();
        $user2->setRoles(['ROLE_PROGRAMMER']);
        $user2->setIsConfirmed(false);

        $user3 = new User();
        $user3->setRoles(['ROLE_PROGRAMMER']);
        $user3->setId(1);
        $user3->setIsConfirmed(true);

        return array(
            array(1, 1, $user1, 1, 403, "You dont have permissions to do this"),
            array(1, 1, $user2, 1, 403, "You must wait until administrator has confirmed your registration"),
            array(1, 1, $user3, 1, 403, "You cant view bug on projects that you dont belong"),
            array(2, 1, $user3, 1, 404, "Project not found"),
            array(1, 2, $user3, 1, 403, "You cant view bug on another programmer behalf")

        );
    }

    /**
     * @dataProvider provideUserDataToViewBug
     */
    public function testGetBugFailure($projectId, $programmerId, $user, $bugId, $errorCode, $errorMessage)
    {
        $response = $this->bugController->GetBug($projectId, $programmerId, $user, $bugId);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals($errorCode, $response->getStatusCode());
        $this->assertEquals($errorMessage, $responseBody['errors']);
    }

    public function testBugNotFound()
    {
        $response = $this->bugController->GetBug(1, 1, $this->user, 10);
        $this->assertEquals(404, $response->getStatusCode());
        $response = $this->bugController->DeleteBug(1, 1, $this->user, 10);
        $this->assertEquals(404, $response->getStatusCode());
        $response = $this->bugController->UpdateBug(null, 1, 1, $this->user, 10);
        $this->assertEquals(404, $response->getStatusCode());
        $response = $this->bugController->GetBug(1, 1, $this->user, 10);
        $this->assertEquals(404, $response->getStatusCode());
        $response = $this->bugController->MarkAsFixed(1, 1, $this->user, 10);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testGetBugsSuccess()
    {
        $response = $this->bugController->GetBugs(1, 1, $this->user);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(2, count($responseBody));
        $this->assertEquals(count($responseBody['ResponsibleForBugs']), count($responseBody['ResponsibleForBugs']));

    }

    public static function provideUserData()
    {
        $user1 = new User();

        $user2 = new User();
        $user2->setRoles(['ROLE_PROGRAMMER']);
        $user2->setIsConfirmed(false);

        $user3 = new User();
        $user3->setRoles(['ROLE_PROGRAMMER']);
        $user3->setId(1);
        $user3->setIsConfirmed(true);

        return array(
            array(1, 1, $user1, 403, "You dont have permissions to do this"),
            array(1, 1, $user2, 403, "You must wait until administrator has confirmed your registration"),
            array(1, 1, $user3, 403, "You cant view bugs of projects that you dont belong"),
            array(2, 1, $user3, 404, "Project not found"),
            array(1, 2, $user3, 403, "You cant view bugs on another programmer behalf")
        );
    }

    /**
     * @dataProvider provideUserData
     */
    public function testGetBugsFailure($projectId, $programmerId, $user, $errorCode, $errorMessage)
    {
        $response = $this->bugController->GetBugs($projectId, $programmerId, $user);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals($errorCode, $response->getStatusCode());
        $this->assertEquals($errorMessage, $responseBody['errors']);
    }


    public function testAddBugSuccess()
    {
        $body = array(
            'description' => "bad UI",
            'severity' => "Low",
            'status'  => "Open"
        );


        $response = $this->bugController->AddBug($body, 1, 1, $this->user);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("bad UI", $responseBody['description']);
        $this->assertEquals("Low", $responseBody['severity']);
        $this->assertEquals("Open", $responseBody['status']);

        $body = array(
            'description' => "bad UI",
            'severity' => "Medium",
            'status'  => "Fixed",
            'responsibility_id' => 1
        );

        $response = $this->bugController->AddBug($body, 1, 1, $this->user);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("bad UI", $responseBody['description']);
        $this->assertEquals("Medium", $responseBody['severity']);
        $this->assertEquals("Fixed", $responseBody['status']);
    }

    public static function provideUserNBodyData()
    {
        $body = array(
            'description' => "bad UI",
            'severity' => "Low",
            'status'  => "Open"
        );

        $user1 = new User();

        $user2 = new User();
        $user2->setRoles(['ROLE_PROGRAMMER']);
        $user2->setIsConfirmed(false);

        $user3 = new User();
        $user3->setRoles(['ROLE_PROGRAMMER']);
        $user3->setId(1);
        $user3->setIsConfirmed(true);

        return array(
            array($body, 1, 1, $user1, 403, "You dont have permissions to do this"),
            array($body, 1, 1, $user2, 403, "You must wait until administrator has confirmed your registration"),
            array($body, 1, 1, $user3, 403, "You cant submit bugs on projects that you dont belong"),
            array($body, 2, 1, $user3, 404, "Project not found"),
            array($body, 1, 2, $user3, 403, "You cant submit bugs on another programmer behalf")

        );
    }

    /**
     * @dataProvider provideUserNBodyData
     */
    public function testAddBugFailure($body,$projectId, $programmerId, $user, $errorCode, $errorMessage)
    {
        $response = $this->bugController->AddBug($body,$projectId, $programmerId, $user);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals($errorCode, $response->getStatusCode());
        $this->assertEquals($errorMessage, $responseBody['errors']);
    }

    public function testNoResponsibleProgrammer()
    {
        $body = array(
            'description' => "bad UI",
            'severity' => "Low",
            'status'  => "Open",
            'responsibility_id' => 2
        );

        $response = $this->bugController->AddBug($body, 1, 1, $this->user);
        $this->assertEquals(404, $response->getStatusCode());

        $response = $this->bugController->UpdateBug($body, 1, 1, $this->user, 1);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testWrongBody()
    {
        $body = array(
            'description' => "bad UI",
            'severity' => "Low"
        );

        $response = $this->bugController->AddBug($body, 1, 1, $this->user);
        $this->assertEquals(422, $response->getStatusCode());
        $response = $this->bugController->UpdateBug($body, 1, 1, $this->user, 1);
        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testDeleteBug()
    {
        $response = $this->bugController->DeleteBug(1, 1, $this->user, 1);
        $this->assertEquals(204, $response->getStatusCode());
    }

    public static function provideUserDataForDeletion()
    {
        $user1 = new User();

        $user2 = new User();
        $user2->setRoles(['ROLE_PROGRAMMER']);
        $user2->setIsConfirmed(false);

        $user3 = new User();
        $user3->setRoles(['ROLE_PROGRAMMER']);
        $user3->setId(1);
        $user3->setIsConfirmed(true);

        return array(
            array(1, 1, $user1, 1, 403, "You dont have permissions to do this"),
            array(1, 1, $user2, 1, 403, "You must wait until administrator has confirmed your registration"),
            array(1, 1, $user3, 1, 403, "You cant delete bugs on projects that you dont belong"),
            array(2, 1, $user3, 1, 404, "Project not found"),
            array(1, 2, $user3, 1, 403, "You cant delete bugs on another programmer behalf")
        );
    }

    /**
     * @dataProvider provideUserDataForDeletion
     */
    public function testDeleteBugFailure($projectId, $programmerId, $user, $bugId, $errorCode, $errorMessage)
    {
        $response = $this->bugController->DeleteBug($projectId, $programmerId, $user, $bugId);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals($errorCode, $response->getStatusCode());
        $this->assertEquals($errorMessage, $responseBody['errors']);
    }

    public function testUpdateBugSuccess()
    {
        $body = array(
            'description' => "bad UI",
            'severity' => "Low",
            'status'  => "Open"
        );

        $response = $this->bugController->UpdateBug($body, 1, 1, $this->user, 1);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("bad UI", $responseBody['description']);
        $this->assertEquals("Low", $responseBody['severity']);
        $this->assertEquals("Open", $responseBody['status']);

        $body = array(
            'description' => "bad routing",
            'severity' => "High",
            'status'  => "Fixed",
            'responsibility_id' => 1
        );

        $response = $this->bugController->UpdateBug($body, 1, 1, $this->user, 1);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("bad routing", $responseBody['description']);
        $this->assertEquals("High", $responseBody['severity']);
        $this->assertEquals("Fixed", $responseBody['status']);
    }

    public static function provideDataForUpdate()
    {
        $body = array(
            'description' => "bad UI",
            'severity' => "Low",
            'status'  => "Open"
        );

        $user1 = new User();

        $user2 = new User();
        $user2->setRoles(['ROLE_PROGRAMMER']);
        $user2->setIsConfirmed(false);

        $user3 = new User();
        $user3->setRoles(['ROLE_PROGRAMMER']);
        $user3->setId(1);
        $user3->setIsConfirmed(true);

        return array(
            array($body, 1, 1, $user1, 1, 403, "You dont have permissions to do this"),
            array($body, 1, 1, $user2, 1, 403, "You must wait until administrator has confirmed your registration"),
            array($body, 1, 1, $user3, 1, 403, "You cant update bugs on projects that you dont belong"),
            array($body, 2, 1, $user3, 1, 404, "Project not found"),
            array($body, 1, 2, $user3, 1, 403, "You cant update bugs on another programmer behalf")

        );
    }

    /**
     * @dataProvider provideDataForUpdate
     */
    public function testUpdateBugFailure($body, $projectId, $programmerId, $user, $bugId, $errorCode, $errorMessage)
    {
        $response = $this->bugController->UpdateBug($body,$projectId, $programmerId, $user, $bugId);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals($errorCode, $response->getStatusCode());
        $this->assertEquals($errorMessage, $responseBody['errors']);
    }

    public function testMarkAsFixedSuccess()
    {
        $response = $this->bugController->MarkAsFixed(1, 1, $this->user, 1);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Fixed", $responseBody['status']);
    }

    public static function provideUserDataToMarkBugs()
    {
        $user1 = new User();

        $user2 = new User();
        $user2->setRoles(['ROLE_PROGRAMMER']);
        $user2->setIsConfirmed(false);

        $user3 = new User();
        $user3->setRoles(['ROLE_PROGRAMMER']);
        $user3->setId(1);
        $user3->setIsConfirmed(true);

        return array(
            array(1, 1, $user1, 1, 403, "You dont have permissions to do this"),
            array(1, 1, $user2, 1, 403, "You must wait until administrator has confirmed your registration"),
            array(1, 1, $user3, 1, 403, "You cant check as finished bugs on projects that you dont belong"),
            array(2, 1, $user3, 1, 404, "Project not found"),
            array(1, 2, $user3, 1, 403, "You cant check as finished bugs on another programmer behalf")

        );
    }

    /**
     * @dataProvider provideUserDataToMarkBugs
     */
    public function testMarkedAsFixedFailure($projectId, $programmerId, $user, $bugId, $errorCode, $errorMessage)
    {
        $response = $this->bugController->MarkAsFixed($projectId, $programmerId, $user, $bugId);
        $responseBody = json_decode($response->getContent(), true);
        $this->assertEquals($errorCode, $response->getStatusCode());
        $this->assertEquals($errorMessage, $responseBody['errors']);
    }
}
