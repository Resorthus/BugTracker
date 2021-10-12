<?php

namespace App\Controller;

use App\Entity\Programmer;
use App\Entity\Project;
use App\Form\ProgrammerType;
use App\Repository\ProgrammerRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/projects")
 */
class ProgrammerController extends AbstractController
{
    /**
     * @Route("/{projectId}/programmers", name="programmers", methods={"GET"})
     */
    public function GetProgrammers(ProjectRepository $projectRepository, $projectId): Response
    {
        $project = $projectRepository->find($projectId);

        if (!$project)
        {
            $data = [
                'errors' => "Project not found",
            ];
            return $this->json($data, 404);
        }

        $programmers = $project->getProgrammers();

        return $this->json($programmers, Response::HTTP_OK, [], [
            ObjectNormalizer::GROUPS => ['show_programmer', 'project_id', 'bug_id']
        ]);
    }

    /**
     * @Route("/{projectId}/programmers", name="programmer_new", methods={"POST"})
     */
    public function AddProgrammer(Request $request, EntityManagerInterface $entityManager, $projectId,
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
                !$request->get('First_Name') ||
                !$request->get('Last_Name')||
                !$request->get('Birthdate') ||
                !$request->get('Level')||
                !$request->get('Specialization')||
                !$request->get('Technology'))
            {
                throw new \Exception();
            }

            $programmer = new Programmer();
            $programmer->setFirstName($request->get('First_Name'));
            $programmer->setLastName($request->get('Last_Name'));
            $programmer->setBirthdate(new \DateTime($request->get('Birthdate')));
            $programmer->setLevel($request->get('Level'));
            $programmer->setSpecialization($request->get('Specialization'));
            $programmer->setTechnology($request->get('Technology'));

            $project->addProgrammer($programmer);
            $programmer->addProject($project);

            $entityManager->persist($programmer);
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->json($programmer, Response::HTTP_OK,[],[
                ObjectNormalizer::GROUPS => ['show_programmer', 'project_id', 'bug_id']
            ]);

        }catch (\Exception $e){
            $data = [
                'errors' => "Invalid Data",
            ];
            return $this->json($data, 422);
        }
    }

    /**
     * @Route("/{projectId}/programmers/{programmerId}", name="programmer_show", methods={"GET"})
     */
    public function GetProgrammer(ProjectRepository $projectRepository, ProgrammerRepository  $programmerRepository,
                                  $projectId, $programmerId): Response
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

        return $this->json($programmer, Response::HTTP_OK,[],[
            ObjectNormalizer::GROUPS => ['show_programmer', 'project_id', 'bug_id']
        ]);
    }

    /**
     * @Route("/{projectId}/programmers/{programmerId}", name="programmer_edit", methods={"PUT"})
     */
    public function UpdateProgrammer(Request $request, $projectId, $programmerId, EntityManagerInterface $entityManager,
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
                !$request->get('First_Name') ||
                !$request->get('Last_Name')||
                !$request->get('Birthdate') ||
                !$request->get('Level')||
                !$request->get('Specialization')||
                !$request->get('Technology'))
            {
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

            $programmer->setFirstName($request->get('First_Name'));
            $programmer->setLastName($request->get('Last_Name'));
            $programmer->setBirthdate(new \DateTime($request->get('Birthdate')));
            $programmer->setLevel($request->get('Level'));
            $programmer->setSpecialization($request->get('Specialization'));
            $programmer->setTechnology($request->get('Technology'));

            $entityManager->persist($programmer);
            $entityManager->flush();

            return $this->json($programmer, Response::HTTP_OK,[],[
                ObjectNormalizer::GROUPS => ['show_programmer', 'project_id', 'bug_id']
            ]);

        }catch (\Exception $e){
            $data = [
                'errors' => "Invalid Data",
            ];
            return $this->json($data, 422);
        }
    }

    /**
     * @Route("/{projectId}/programmers/{programmerId}", name="programmer_delete", methods={"DELETE"})
     */
    public function DeleteProgrammer(EntityManagerInterface $entityManager, ProjectRepository $projectRepository, $projectId,
                              $programmerId): Response
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

        /**@var Programmer $programmer*/
        foreach ($programmer->getSubmittedBugs() as $bug)
        {
            $entityManager->remove($bug);
        }
        $entityManager->remove($programmer);
        $entityManager->flush();

        return $this->json($programmer, Response::HTTP_OK,[],[
            ObjectNormalizer::GROUPS => ['delete_programmer', 'project_id']
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
