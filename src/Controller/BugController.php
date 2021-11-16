<?php

namespace App\Controller;

use App\Entity\Bug;
use App\Entity\Programmer;
use App\Entity\Project;
use App\Entity\User;
use App\Form\BugType;
use App\Repository\BugRepository;
use App\Repository\ProgrammerRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/projects")
 */
class BugController extends AbstractController
{
    /**
     * @Route("/{projectId}/programmers/{programmerId}/bugs", name="bugs", methods={"GET"})
     */
    public function GetBugs(ProjectRepository $projectRepository, $projectId, $programmerId): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_PROGRAMMER', $roles))
        {

            $project = $projectRepository->find($projectId);

            if (!$project) {
                $data = [
                    'errors' => "Project not found",
                ];
                return $this->json($data, 404);
            }

            if (!$user->getProjects()->contains($project))
            {
                $data = [
                    'errors' => "You cant view bugs of projects that you dont belong",
                ];
                return $this->json($data, 403);
            }


            if ($user->getId() != $programmerId) {
                $data = [
                    'errors' => "You cant view bugs on another programmer behalf",
                ];
                return $this->json($data, 403);
            }


            $bugs = [
                   'SubmittedBugs' => $user->getSubmittedBugs(),
                    'ResponsibleForBugs' => $user->getBugs(),
            ];

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
     * @Route("/{projectId}/programmers/{programmerId}/bugs/{bugId}", name="mark_as_fixed", methods={"PATCH"})
     */
    public function MarkAsFixed(ProjectRepository $projectRepository, EntityManagerInterface  $entityManager,
                                $projectId, $programmerId, $bugId): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_PROGRAMMER', $roles))
        {

            $project = $projectRepository->find($projectId);

            if (!$project) {
                $data = [
                    'errors' => "Project not found",
                ];
                return $this->json($data, 404);
            }

            if (!$user->getProjects()->contains($project))
            {
                $data = [
                    'errors' => "You cant check as finished bugs on projects that you dont belong",
                ];
                return $this->json($data, 403);
            }


            if ($user->getId() != $programmerId) {
                $data = [
                    'errors' => "You cant check as finished bugs on another programmer behalf",
                ];
                return $this->json($data, 403);
            }

            /**@var Bug $bug*/
            $bug = $user->getBugs()->filter(function($element) use ($bugId, $projectId)
            {
                /**@var Bug $element*/
                return $element->getId() == $bugId && $element->getProject()->getId() == $projectId;
            })->first();

            if (!$bug)
            {
                $data = [
                    'errors' => "Bug not found",
                ];
                return $this->json($data, 404);
            }

            $bug->setStatus("Fixed");

            $entityManager->persist($bug);
            $entityManager->flush();


            return $this->json($bug, Response::HTTP_OK, [], [
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
     * @Route("/{projectId}/programmers/{programmerId}/bugs", name="bug_new", methods={"POST"})
     */
    public function AddBug(Request $request, EntityManagerInterface $entityManager, $projectId, $programmerId,
                                  ProjectRepository $projectRepository): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_PROGRAMMER', $roles))
        {

            try {

                $project = $projectRepository->find($projectId);

                if (!$project) {
                    $data = [
                        'errors' => "Project not found",
                    ];
                    return $this->json($data, 404);
                }

                if (!$user->getProjects()->contains($project))
                {
                    $data = [
                        'errors' => "You cant submit bugs on projects that you dont belong",
                    ];
                    return $this->json($data, 403);
                }


                if ($user->getId() != $programmerId) {
                    $data = [
                        'errors' => "You cant submit bugs on another programmer behalf",
                    ];
                    return $this->json($data, 403);
                }

                $request = $this->transformJsonBody($request);

                if (!$request ||
                    !$request->get('description') ||
                    !$request->get('severity') ||
                    !$request->get('status')) {
                    throw new \Exception("Missing data");
                }

                $responsibleProgrammer = new User();

                if ($request->get('responsibility_id')) {
                    $responsibleProgrammerId = $request->get('responsibility_id');
                    $responsibleProgrammer = $project->getUsers()->filter(function ($element) use ($responsibleProgrammerId) {
                        return $element->getId() == $responsibleProgrammerId;
                    })->first();

                    if (!$responsibleProgrammer) {
                        $data = [
                            'errors' => "Programmer responsible for bugfixing not found",
                        ];
                        return $this->json($data, 404);
                    }
                }

                $bug = new Bug();

                if ($request->get('responsibility_id'))
                    $bug->setResponsibleUser($responsibleProgrammer);
                else
                    $bug->setResponsibleUser(null);
                $bug->setSubmitter($user);
                $bug->setDescription($request->get('description'));
                $bug->setSeverity($request->get('severity'));
                $bug->setStatus($request->get('status'));
                $bug->setDate(new \DateTime());
                $bug->setProject($project);

                $user->addSubmittedBug($bug);
                $project->addBug($bug);
                if ($request->get('responsibility_id'))
                {
                    $responsibleProgrammer->addBug($bug);
                    $entityManager->persist($responsibleProgrammer);
                }

                $entityManager->persist($user);
                $entityManager->persist($bug);
                $entityManager->persist($project);
                $entityManager->flush();

                return $this->json($bug, Response::HTTP_OK,[],[
                    ObjectNormalizer::GROUPS => ['show_bug', 'programmer_id', 'project_id']
                ]);

            }
            catch (\Exception $e){
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
     * @Route("/{projectId}/programmers/{programmerId}/bugs/{bugId}", name="bug_show", methods={"GET"})
     */
    public function GetBug(ProjectRepository $projectRepository, ProgrammerRepository  $programmerRepository,
                                  $projectId, $programmerId, $bugId): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_PROGRAMMER', $roles))
        {

            $project = $projectRepository->find($projectId);

            if (!$project) {
                $data = [
                    'errors' => "Project not found",
                ];
                return $this->json($data, 404);
            }

            if (!$user->getProjects()->contains($project))
            {
                $data = [
                    'errors' => "You cant check as finished bugs on projects that you dont belong",
                ];
                return $this->json($data, 403);
            }


            if ($user->getId() != $programmerId) {
                $data = [
                    'errors' => "You cant check as finished bugs on another programmer behalf",
                ];
                return $this->json($data, 403);
            }

            /**@var Bug $bug*/
            $bug = $user->getSubmittedBugs()->filter(function($element) use ($bugId, $projectId)
            {
                /**@var Bug $element*/
                return $element->getId() == $bugId && $element->getProject()->getId() == $projectId;
            })->first();

            if (!$bug)
            {
                $data = [
                    'errors' => "Bug not found",
                ];
                return $this->json($data, 404);
            }

            return $this->json($bug, Response::HTTP_OK,[],[
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
     * @Route("/{projectId}/programmers/{programmerId}/bugs/{bugId}", name="bug_edit", methods={"PUT"})
     */
    public function UpdateBug(Request $request, $projectId, $bugId, $programmerId, EntityManagerInterface $entityManager,
                                     ProjectRepository $projectRepository): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_PROGRAMMER', $roles))
        {
            try{
                $project = $projectRepository->find($projectId);

                if (!$project) {
                    $data = [
                        'errors' => "Project not found",
                    ];
                    return $this->json($data, 404);
                }

                if (!$user->getProjects()->contains($project))
                {
                    $data = [
                        'errors' => "You cant submit bugs on projects that you dont belong",
                    ];
                    return $this->json($data, 403);
                }


                if ($user->getId() != $programmerId) {
                    $data = [
                        'errors' => "You cant submit bugs on another programmer behalf",
                    ];
                    return $this->json($data, 403);
                }

                /**@var Bug $bug*/
                $bug = $user->getSubmittedBugs()->filter(function($element) use ($bugId, $projectId)
                {
                    /**@var Bug $element*/
                    return $element->getId() == $bugId && $element->getProject()->getId() == $projectId;
                })->first();

                if (!$bug)
                {
                    $data = [
                        'errors' => "Bug not found",
                    ];
                    return $this->json($data, 404);
                }

                $request = $this->transformJsonBody($request);

                if (!$request ||
                    !$request->get('description') ||
                    !$request->get('severity') ||
                    !$request->get('status')) {
                    throw new \Exception("Missing data");
                }

                $responsibleProgrammer = new User();

                if ($request->get('responsibility_id')) {
                    $responsibleProgrammerId = $request->get('responsibility_id');
                    $responsibleProgrammer = $project->getUsers()->filter(function ($element) use ($responsibleProgrammerId) {
                        return $element->getId() == $responsibleProgrammerId;
                    })->first();

                    if (!$responsibleProgrammer) {
                        $data = [
                            'errors' => "Programmer responsible for bugfixing not found",
                        ];
                        return $this->json($data, 404);
                    }
                }

                if ($request->get('responsibility_id'))
                    $bug->setResponsibleUser($responsibleProgrammer);
                else
                    $bug->setResponsibleUser(null);
                $bug->setSubmitter($user);
                $bug->setDescription($request->get('description'));
                $bug->setSeverity($request->get('severity'));
                $bug->setStatus($request->get('status'));
                $bug->setDate(new \DateTime());

                $user->addSubmittedBug($bug);
                if ($request->get('responsibility_id'))
                {
                    $responsibleProgrammer->addBug($bug);
                    $entityManager->persist($responsibleProgrammer);
                }

                $entityManager->persist($user);
                $entityManager->persist($bug);
                $entityManager->flush();

                return $this->json($bug, Response::HTTP_OK,[],[
                    ObjectNormalizer::GROUPS => ['show_bug', 'programmer_id', 'project_id']
                ]);

            }
            catch (\Exception $e){
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
     * @Route("/{projectId}/programmers/{programmerId}/bugs/{bugId}", name="bug_delete", methods={"DELETE"})
     */
    public function DeleteBug(EntityManagerInterface $entityManager, ProjectRepository $projectRepository, $projectId,
                           $programmerId, $bugId): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_PROGRAMMER', $roles))
        {

            $project = $projectRepository->find($projectId);

            if (!$project) {
                $data = [
                    'errors' => "Project not found",
                ];
                return $this->json($data, 404);
            }

            if (!$user->getProjects()->contains($project))
            {
                $data = [
                    'errors' => "You cant check as finished bugs on projects that you dont belong",
                ];
                return $this->json($data, 403);
            }


            if ($user->getId() != $programmerId) {
                $data = [
                    'errors' => "You cant check as finished bugs on another programmer behalf",
                ];
                return $this->json($data, 403);
            }

            /**@var Bug $bug*/
            $bug = $user->getSubmittedBugs()->filter(function($element) use ($bugId, $projectId)
            {
                /**@var Bug $element*/
                return $element->getId() == $bugId && $element->getProject()->getId() == $projectId;
            })->first();

            if (!$bug)
            {
                $data = [
                    'errors' => "Bug not found",
                ];
                return $this->json($data, 404);
            }

            $entityManager->remove($bug);
            $entityManager->flush();

            return $this->json($bug, 204,[],[
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
