<?php

namespace App\Controller;

use App\Entity\Programmer;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
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
     * @Route("/", name="projects", methods={"GET"})
     */
    public function GetProjects(ProjectRepository $projectRepository): Response
    {
        $projects = $projectRepository->findAll();

        return $this->json($projects, Response::HTTP_OK, [], [
            ObjectNormalizer::GROUPS => ['show_project', 'programmer_id']
        ]);
    }

    /**
     * @Route("/", name="project_new", methods={"POST"})
     */
    public function AddProject(Request $request, EntityManagerInterface $entityManager): Response
    {
        try{
            $request = $this->transformJsonBody($request);

            if (!$request ||
                !$request->get('Name') ||
                !$request->get('Deadline')||
                !$request->get('ProgrammerCount'))
            {
                throw new \Exception();
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
                'errors' => "Invalid Data",
            ];
            return $this->json($data, 422);
        }
    }

    /**
     * @Route("/{projectId}", name="project_show", methods={"GET"})
     */
    public function GetProject(ProjectRepository $projectRepository, $projectId): Response
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

    /**
     * @Route("/{projectId}", name="project_edit", methods={"PUT"})
     */
    public function UpdateProject(Request $request, $projectId, EntityManagerInterface $entityManager, ProjectRepository $projectRepository): Response
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
                !$request->get('Name') ||
                !$request->get('Deadline')||
                !$request->get('ProgrammerCount'))
            {
                throw new \Exception();
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
                'errors' => "Invalid Data",
            ];
            return $this->json($data, 422);
        }
    }

    /**
     * @Route("/{projectId}", name="project_delete", methods={"DELETE"})
     */
    public function DeleteProgrammer(EntityManagerInterface $entityManager, ProjectRepository $projectRepository,
                                     $projectId): Response
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

        foreach ($project->getProgrammers() as $programmer)
        {
            $programmer->removeProject($project);
            $entityManager->persist($programmer);
        }

        $entityManager->remove($project);
        $entityManager->flush();

        return $this->json($project, Response::HTTP_OK,[],[
            ObjectNormalizer::GROUPS => ['delete_project']
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
