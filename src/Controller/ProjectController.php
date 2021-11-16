<?php

namespace App\Controller;

use App\Entity\Bug;
use App\Entity\Programmer;
use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectType;
use App\Repository\BugRepository;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Json;

/**
 * @Route("/projects")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/{projectId}/bugs", name="project_bugs", methods={"GET"})
     */
    public function GetProjectBugs(Request $request, ProjectRepository $projectRepository, $projectId): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_SUPERVISOR', $roles))
        {

            if ($user->getIsConfirmed() == false)
            {
                $data = [
                    'errors' => "You must wait until administrator has confirmed your registration",
                ];
                return $this->json($data, 403);
            }

            $project = $projectRepository->find($projectId);

            if (!$project)
            {
                $data = [
                    'errors' => "Project not found",
                ];
                return $this->json($data, 404);
            }

            $bugs = $project->getBugs();

            return $this->json($bugs, Response::HTTP_OK, [], [
                ObjectNormalizer::GROUPS => ['show_bug', 'programmer_id', 'project_id']
            ]);
        }
        else
        {
            $data = [
                'errors' => "You dont have permissions to do this",
            ];
            return $this->json($data, 403);
        }
    }


    /**
     * @Route("/", name="projects", methods={"GET"})
     */
    public function GetProjects(Request $request, ProjectRepository $projectRepository): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_SUPERVISOR', $roles))
        {

            if ($user->getIsConfirmed() == false)
            {
                $data = [
                    'errors' => "You must wait until administrator has confirmed your registration",
                ];
                return $this->json($data, 403);
            }

            $projects = $projectRepository->findAll();

            return $this->json($projects, Response::HTTP_OK, [], [
                ObjectNormalizer::GROUPS => ['show_project', 'programmer_id']
            ]);
        }
        else
            {
                $data = [
                    'errors' => "You dont have permissions to do this",
                ];
                return $this->json($data, 403);
            }
    }

    /**
     * @Route("/", name="project_new", methods={"POST"})
     */
    public function AddProject(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles))
        {

            try{
                $request = $this->transformJsonBody($request);

                if (!$request)
                {
                    throw new \Exception("Missing data");
                }

                if (!$request->get('Name'))
                {
                    throw new \Exception("Missing name");
                }

                if (!$request->get('Deadline'))
                {
                    throw new \Exception("Missing deadline");
                }

                if (!$request->get('ProgrammerCount'))
                {
                    throw new \Exception("Missing programmer count");
                }

                $project = new Project();
                $project->setName($request->get('Name'));
                $project->setDeadline(new \DateTime($request->get('Deadline')));
                $project->setProgrammerCount($request->get('ProgrammerCount'));
                $entityManager->persist($project);
                $entityManager->flush();

                return $this->json($project, Response::HTTP_OK,[],[
                    ObjectNormalizer::GROUPS => ['show_project', 'programmer_id']
                ]);

            }catch (\Exception $e){
                $data = [
                    'errors' => $e->getMessage(),
                ];
                return $this->json($data, 422);
            }
        }
        else
        {
            $data = [
                'errors' => "You dont have permissions to do this",
            ];
            return $this->json($data, 403);
        }

    }

    /**
     * @Route("/{projectId}", name="project_show", methods={"GET"})
     */
    public function GetProject(ProjectRepository $projectRepository, $projectId): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles))
        {
            $project = $projectRepository->find($projectId);

            if (!$project)
            {
                $data = [
                    'errors' => "Project not found",
                ];
                return $this->json($data, 404);
            }

            return $this->json($project, Response::HTTP_OK,[],[
                ObjectNormalizer::GROUPS => ['show_project', 'programmer_id', 'bug_id']
            ]);
        }
        else
        {
            $data = [
                'errors' => "You dont have permissions to do this",
            ];
            return $this->json($data, 403);
        }
    }

    /**
     * @Route("/{projectId}", name="project_edit", methods={"PUT"})
     */
    public function UpdateProject(Request $request, $projectId, EntityManagerInterface $entityManager, ProjectRepository $projectRepository): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles))
        {

            try{
                $project = $projectRepository->find($projectId);
                if (!$project)
                {
                    $data = [
                        'errors' => "Project not found",
                    ];
                    return $this->json($data, 404);
                }

                $request = $this->transformJsonBody($request);

                if (!$request)
                {
                    throw new \Exception("Missing data");
                }

                if (!$request->get('Name'))
                {
                    throw new \Exception("Missing name");
                }

                if (!$request->get('Deadline'))
                {
                    throw new \Exception("Missing deadline");
                }

                if (!$request->get('ProgrammerCount'))
                {
                    throw new \Exception("Missing programmer count");
                }

                $project->setName($request->get('Name'));
                $project->setDeadline(new \DateTime($request->get('Deadline')));
                $project->setProgrammerCount($request->get('ProgrammerCount'));
                $entityManager->persist($project);
                $entityManager->flush();

                return $this->json($project, Response::HTTP_OK,[],[
                    ObjectNormalizer::GROUPS => ['show_project', 'programmer_id']
                ]);

            }catch (\Exception $e){
                $data = [
                    'errors' => $e->getMessage(),
                ];
                return $this->json($data, 422);
            }
        }
        else
        {
            $data = [
                'errors' => "You dont have permissions to do this",
            ];
            return $this->json($data, 403);
        }

    }

    /**
     * @Route("/{projectId}", name="project_delete", methods={"DELETE"})
     */
    public function DeleteProject(EntityManagerInterface $entityManager, ProjectRepository $projectRepository,
                                     $projectId): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles))
        {

            $project = $projectRepository->find($projectId);

            if (!$project)
            {
                $data = [
                    'errors' => "Project not found",
                ];
                return $this->json($data, 404);
            }


            foreach ($project->getBugs() as $bug)
            {
                $entityManager->remove($bug);
            }

            foreach ($project->getUsers() as $programmer)
            {
                $programmer->removeProject($project);
                $entityManager->persist($programmer);
            }

            $entityManager->remove($project);
            $entityManager->flush();

            return $this->json($project, 204,[],[
                ObjectNormalizer::GROUPS => ['delete_project']
            ]);
        }
        else
        {
            $data = [
                'errors' => "You dont have permissions to do this",
            ];
            return $this->json($data, 403);
        }

    }

    /**
     * @Route("/{projectId}/programmers/{programmerId}", name="manage_programmer", methods={"PATCH"})
     */
    public function EditProjectProgrammerParticipation(EntityManagerInterface $entityManager,
                                                       ProjectRepository $projectRepository,
                                                       UserRepository $userRepository,
                                                       $projectId, $programmerId, Request $request): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles))
        {

            $project = $projectRepository->find($projectId);

            if (!$project)
            {
                $data = [
                    'errors' => "Project not found",
                ];
                return $this->json($data, 404);
            }

            $userToFind = $userRepository->find($programmerId);

            if (!$userToFind)
            {
                $data = [
                    'errors' => "User not found",
                ];
                return $this->json($data, 404);
            }

            if (!in_array('ROLE_PROGRAMMER', $userToFind->getRoles()))
            {
                $data = [
                    'errors' => "User is not programmer",
                ];
                return $this->json($data, 409);
            }

            $request = $this->transformJsonBody($request);

            if (!$request)
            {
                $data = [
                    'errors' => "No data",
                ];
                return $this->json($data, 404);
            }

            if (!$request->get('action'))
            {
                $data = [
                    'errors' => "No action specified",
                ];
                return $this->json($data, 404);
            }

            $action = $request->get('action');
            if ($action != "add" && $action != "delete")
            {
                $data = [
                    'errors' => "No suitable action found",
                ];
                return $this->json($data, 404);
            }

            if ($action == "add")
            {
                $project->addUser($userToFind);
                $userToFind->addProject($project);
            }

            else
            {
                $programmerBugs = $project->getBugs()->filter(function($element) use ($userToFind)
                {
                    /**@var Bug $element*/
                    return $element->getSubmittedBy()->getId() == $userToFind->getId();
                });

                foreach ($programmerBugs as $b)
                {
                    $entityManager->remove($b);
                }

                $project->removeUser($userToFind);
                $userToFind->removeProject($project);
            }

            $entityManager->persist($project);
            $entityManager->persist($userToFind);
            $entityManager->flush();

            return $this->json($project, 200,[],[
                ObjectNormalizer::GROUPS => ['show_project', 'programmer_id', 'bug_id']
            ]);
        }
        else
        {
            $data = [
                'errors' => "You dont have permissions to do this",
            ];
            return $this->json($data, 403);
        }

    }

    protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}
