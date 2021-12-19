<?php

namespace App\Controller;

use App\Entity\Programmer;
use App\Entity\Project;
use App\Entity\User;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Util\StringUtils;


class UserController extends AbstractController
{

    /**
     * @Route("/api/users", name="users", methods={"GET"})
     */
    public function GetUsers(Request $request, UserRepository $userRepository): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles))
        {

            $users = $userRepository->findAll();
            $notAdminUsers = [];

            foreach ($users as $u)
            {
                $userRoles = $u->getRoles();
                if (!in_array('ROLE_ADMIN', $userRoles))
                {
                    $notAdminUsers[] = $u;
                }
            }

            return $this->json($notAdminUsers, Response::HTTP_OK, [], [
                ObjectNormalizer::SKIP_NULL_VALUES => true,
                ObjectNormalizer::IGNORED_ATTRIBUTES => ['userIdentifier', 'username', 'Projects', 'Bugs', 'SubmittedBugs']
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
     * @Route("/api/users/{userId}", name="user", methods={"GET"})
     */
    public function GetUserInfo(Request $request, UserRepository $userRepository, $userId): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_PROGRAMMER', $roles))
        {

            $userToLook = $userRepository->findByEmail($userId);

            if (count($userToLook) != 1)
            {
                $data = [
                    'errors' => "User not found",
                ];
                return $this->json($data, 404);
            }

            return $this->json($userToLook[0], Response::HTTP_OK,[],[
                ObjectNormalizer::SKIP_NULL_VALUES => true,
                ObjectNormalizer::IGNORED_ATTRIBUTES => ['userIdentifier', 'username', 'Projects', 'Bugs', 'SubmittedBugs']
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
     * @Route("/api/users/{userId}", name="edit_user", methods={"PUT"})
     */
    public function EditUserInfo(Request $request, UserRepository $userRepository, $userId,
                                 EntityManagerInterface $entityManager): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles))
        {

            $userToLook = $userRepository->find($userId);

            if (!$userToLook)
            {
                $data = [
                    'errors' => "User not found",
                ];
                return $this->json($data, 404);
            }

            try{
                $request = $this->transformJsonBody($request);

                if (!$request)
                {
                    throw new \Exception("Missing data");
                }

                if (!$request->get('role'))
                {
                    throw new \Exception("Missing role");
                }

                if (!$request->get('firstname'))
                {
                    throw new \Exception("Missing first name");
                }

                if (!$request->get('lastname'))
                {
                    throw new \Exception("Missing last name");
                }

                if (!$request->get('birthdate'))
                {
                    throw new \Exception("Missing birthdate");
                }

                if (strcmp($request->get('role'), 'ROLE_PROGRAMMER') != 0 && strcmp($request->get('role'), 'ROLE_SUPERVISOR') != 0)
                {
                    throw new \Exception("Incorrect role");
                }


                if ($request->get('role') == 'ROLE_PROGRAMMER') {

                    if (!$request->get('level')) {
                        throw new \Exception("Missing level");
                    }

                    if (!$request->get('specialization')) {
                        throw new \Exception("Missing specialization");
                    }

                    if (!$request->get('technology')) {
                        throw new \Exception("Missing technology");
                    }
                }


            }catch (\Exception $e){
                $data = [
                    'errors' => $e->getMessage(),
                ];
                return $this->json($data, 422);
            }

            $roles = [];
            $roles[] = $request->get('role');
            $userToLook->setRoles($roles);
            $userToLook->setFirstName($request->get('firstname'));
            $userToLook->setLastName($request->get('lastname'));
            $userToLook->setBirthdate(new \DateTime($request->get('birthdate')));

            if ($request->get('role') == 'ROLE_PROGRAMMER')
            {
                $userToLook->setLevel($request->get('level'));
                $userToLook->setSpecialization($request->get('specialization'));
                $userToLook->setTechnology($request->get('technology'));
            }

            else
            {

                $proj = $userToLook->getProjects();
                $bugs = $userToLook->getSubmittedBugs();

                foreach ($proj as $p )
                {
                    $p->removeUser($userToLook);
                    $entityManager->persist($p);
                }

                foreach ($bugs as $b )
                {
                    $entityManager->remove($b);
                }

            }

            $entityManager->persist($userToLook);
            $entityManager->flush();

            return $this->json($userToLook, Response::HTTP_OK,[],[
                ObjectNormalizer::SKIP_NULL_VALUES => true,
                ObjectNormalizer::IGNORED_ATTRIBUTES => ['userIdentifier', 'username', 'Projects', 'Bugs', 'SubmittedBugs']
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
     * @Route("/api/users/{userId}", name="delete_user", methods={"DELETE"})
     */
    public function DeleteUser(Request $request, UserRepository $userRepository, $userId,
                                 EntityManagerInterface $entityManager): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles))
        {

            $userToLook = $userRepository->find($userId);

            if (!$userToLook)
            {
                $data = [
                    'errors' => "User not found",
                ];
                return $this->json($data, 404);
            }


            $entityManager->remove($userToLook);
            $entityManager->flush();

            return $this->json($userToLook, 204,[],[
                ObjectNormalizer::GROUPS => ['delete_programmer', 'project_id']
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
     * @Route("/api/users/{userId}", name="confirmUser", methods={"PATCH"})
     */
    public function ConfirmUser(Request $request, UserRepository $userRepository, $userId,
                                EntityManagerInterface $entityManager): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        try
        {
            if (in_array('ROLE_ADMIN', $roles))
            {

                $user = $userRepository->find($userId);
                if (!$user)
                {
                    throw new \Exception("User not found");
                }

                if ($user->getIsConfirmed() == true)
                {
                    throw new \Exception("User is already confirmed");
                }

                $user->setIsConfirmed(true);
                $entityManager ->persist($user);
                $entityManager->flush();

                return $this->json($user, Response::HTTP_OK, [], [
                    ObjectNormalizer::SKIP_NULL_VALUES => true,
                    ObjectNormalizer::IGNORED_ATTRIBUTES => ['userIdentifier', 'username', 'Projects', 'Bugs', 'SubmittedBugs']
                ]);
            }
            else {
                $data = [
                    'errors' => "You dont have permissions to do this",
                ];
                return $this->json($data, 403);
            }
        }
        catch (\Exception $e)
        {
            $data = [
                'errors' => $e->getMessage(),
            ];

            if ($e->getMessage() == "User not found")
                return $this->json($data, 404);
            if ($e->getMessage() == "User is already confirmed")
                return $this->json($data, 409);
        }
    }

    /**
     * @Route("/api/users/{userId}/projects", name="user_projects", methods={"GET"})
     */
    public function GetProgrammerProjects(Request $request, ProjectRepository $projectRepository, $userId): Response
    {
        $token = $this->get('security.token_storage')->getToken();
        /**@var User $user*/
        $user = $token->getUser();
        $roles = $user->getRoles();
        if (in_array('ROLE_PROGRAMMER', $roles))
        {

            if ($user->getIsConfirmed() == false)
            {
                $data = [
                    'errors' => "You must wait until administrator has confirmed your registration",
                ];
                return $this->json($data, 403);
            }

            if ($user->getId() != $userId)
            {
                $data = [
                    'errors' => "You are trying to view other programmer projects",
                ];
                return $this->json($data, 403);
            }

            $projects = $user->getProjects();

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
