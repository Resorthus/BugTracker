<?php

namespace App\Controller;

use App\Entity\Bug;
use App\Entity\Programmer;
use App\Entity\Project;
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
    public function GetProgrammers(ProjectRepository $projectRepository, $projectId, $programmerId): Response
    {
        $project = $projectRepository->find($projectId);

        if (!$project)
        {
            $data = [
                'errors' => "Project not found",
            ];
            return $this->json($data, 404);
        }

        $programmer = $project->getProgrammers()->filter(function($element) use ($programmerId)
        {
            return $element->getId() == $programmerId;
        })->first();

        if (!$programmer)
        {
            $data = [
                'errors' => "Programmer not found",
            ];
            return $this->json($data, 404);
        }

        /**@var Programmer $programmer
        */
        $bugs = $programmer->getSubmittedBugs()->filter(function($element) use ($projectId)
        {
            /**@var Bug $element*/
            return $element->getProject()->getId() == $projectId;
        });

        return $this->json($bugs, Response::HTTP_OK, [], [
            ObjectNormalizer::GROUPS => ['show_bug', 'programmer_id', 'project_id']
        ]);
    }

    /**
     * @Route("/{projectId}/programmers/{programmerId}/bugs", name="bug_new", methods={"POST"})
     */
    public function AddBug(Request $request, EntityManagerInterface $entityManager, $projectId, $programmerId,
                                  ProjectRepository $projectRepository): Response
    {
        try {

            $project = $projectRepository->find($projectId);

            if (!$project) {
                $data = [
                    'errors' => "Project not found",
                ];
                return $this->json($data, 404);
            }

            $programmer = $project->getProgrammers()->filter(function ($element) use ($programmerId) {
                return $element->getId() == $programmerId;
            })->first();

            if (!$programmer) {
                $data = [
                    'errors' => "Programmer not found",
                ];
                return $this->json($data, 404);
            }

            $request = $this->transformJsonBody($request);

            if (!$request ||
                !$request->get('description') ||
                !$request->get('severity') ||
                !$request->get('status')) {
                throw new \Exception();
            }

            $responsibleProgrammer = new Programmer();

            if ($request->get('responsibility_id')) {
                $responsibleProgrammerId = $request->get('responsibility_id');
                $responsibleProgrammer = $project->getProgrammers()->filter(function ($element) use ($responsibleProgrammerId) {
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
                $bug->setResponsibility($responsibleProgrammer);
            else
                $bug->setResponsibility(null);
            $bug->setSubmittedBy($programmer);
            $bug->setDescription($request->get('description'));
            $bug->setSeverity($request->get('severity'));
            $bug->setStatus($request->get('status'));
            $bug->setDate(new \DateTime());
            $bug->setProject($project);

            /**@var Programmer $programmer */
            $programmer->addSubmittedBug($bug);
            $project->addBug($bug);
            if ($request->get('responsibility_id'))
            {
                $responsibleProgrammer->addBug($bug);
                $entityManager->persist($responsibleProgrammer);
            }

            $entityManager->persist($programmer);
            $entityManager->persist($bug);
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->json($bug, Response::HTTP_OK,[],[
                ObjectNormalizer::GROUPS => ['show_bug', 'programmer_id', 'project_id']
            ]);

        }
        catch (\Exception $e){
            $data = [
                'errors' => "Invalid Data",
            ];
            return $this->json($data, 422);
        }
    }

    /**
     * @Route("/{projectId}/programmers/{programmerId}/bugs/{bugId}", name="bug_show", methods={"GET"})
     */
    public function GetBug(ProjectRepository $projectRepository, ProgrammerRepository  $programmerRepository,
                                  $projectId, $programmerId, $bugId): Response
    {
        $project = $projectRepository->find($projectId);

        if (!$project)
        {
            $data = [
                'errors' => "Project not found",
            ];
            return $this->json($data, 404);
        }

        $programmer = $project->getProgrammers()->filter(function($element) use ($programmerId)
        {
            return $element->getId() == $programmerId;
        })->first();

        if (!$programmer)
        {
            $data = [
                'errors' => "Programmer not found",
            ];
            return $this->json($data, 404);
        }

        /**@var Programmer $programmer */
        $bug = $programmer->getSubmittedBugs()->filter(function($element) use ($bugId, $projectId)
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

    /**
     * @Route("/{projectId}/programmers/{programmerId}/bugs/{bugId}", name="bug_edit", methods={"PUT"})
     */
    public function UpdateProgrammer(Request $request, $projectId, $bugId, $programmerId, EntityManagerInterface $entityManager,
                                     ProjectRepository $projectRepository): Response
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

            if (!$request ||
                !$request->get('description') ||
                !$request->get('severity') ||
                !$request->get('status')) {
                throw new \Exception();
            }

            $programmer = $project->getProgrammers()->filter(function($element) use ($programmerId)
            {
                return $element->getId() == $programmerId;
            })->first();

            if (!$programmer)
            {
                $data = [
                    'errors' => "Programmer not found",
                ];
                return $this->json($data, 404);
            }

            /**@var Programmer $programmer */
            $bug = $programmer->getSubmittedBugs()->filter(function($element) use ($bugId, $projectId)
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

            $responsibleProgrammer = new Programmer();

            if ($request->get('responsibility_id')) {
                $responsibleProgrammerId = $request->get('responsibility_id');
                $responsibleProgrammer = $project->getProgrammers()->filter(function ($element) use ($responsibleProgrammerId) {
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
                $bug->setResponsibility($responsibleProgrammer);
            else
                $bug->setResponsibility(null);
            $bug->setSubmittedBy($programmer);
            $bug->setDescription($request->get('description'));
            $bug->setSeverity($request->get('severity'));
            $bug->setStatus($request->get('status'));
            $bug->setDate(new \DateTime());

            /**@var Programmer $programmer */
            $programmer->addSubmittedBug($bug);
            if ($request->get('responsibility_id'))
            {
                $responsibleProgrammer->addBug($bug);
                $entityManager->persist($responsibleProgrammer);
            }

            $entityManager->persist($programmer);
            $entityManager->persist($bug);
            $entityManager->flush();

            return $this->json($bug, Response::HTTP_OK,[],[
                ObjectNormalizer::GROUPS => ['show_bug', 'programmer_id', 'project_id']
            ]);

        }
        catch (\Exception $e){
            $data = [
                'errors' => "Invalid Data",
            ];
            return $this->json($data, 422);
        }
    }

    /**
     * @Route("/{projectId}/programmers/{programmerId}/bugs/{bugId}", name="bug_delete", methods={"DELETE"})
     */
    public function DeleteBug(EntityManagerInterface $entityManager, ProjectRepository $projectRepository, $projectId,
                           $programmerId, $bugId): Response
    {
        $project = $projectRepository->find($projectId);

        if (!$project)
        {
            $data = [
                'errors' => "Project not found",
            ];
            return $this->json($data, 404);
        }

        $programmer = $project->getProgrammers()->filter(function($element) use ($programmerId)
        {
            return $element->getId() == $programmerId;
        })->first();

        if (!$programmer)
        {
            $data = [
                'errors' => "Programmer not found",
            ];
            return $this->json($data, 404);
        }

        /**@var Programmer $programmer */
        $bug = $programmer->getSubmittedBugs()->filter(function($element) use ($bugId, $projectId)
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

        return $this->json($bug, Response::HTTP_OK,[],[
            ObjectNormalizer::GROUPS => ['bug_delete', 'programmer_id', 'project_id']
        ]);
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
