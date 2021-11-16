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


class RegistrationController extends AbstractController
{

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function Register(Request $request, EntityManagerInterface $entityManager,
                             UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        try{
            $request = $this->transformJsonBody($request);

            if (!$request)
            {
                throw new \Exception("Missing data");
            }

            if (!$request->get('email'))
            {
                throw new \Exception("Missing email");
            }

            if (!$request->get('password'))
            {
                throw new \Exception("Missing password");
            }

            if (!$request->get('repeatpassword'))
            {
                throw new \Exception("Missing repeat password");
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

            if ($request->get('password') != $request->get('repeatpassword'))
            {
                throw new \Exception("Password must match");
            }

            if (!str_contains($request->get('email'), '@'))
            {
                throw new \Exception("Incorrect email");
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

            $email = $request->get('email');
            $users = $userRepository->findByEmail($email);

            if (count($users) == 1)
            {
                {
                    throw new \Exception("User already exists with this email");
                }
            }


        }catch (\Exception $e){
            $data = [
                'errors' => $e->getMessage(),
            ];
            return $this->json($data, 422);
        }

        $user = new User();
        $user->setEmail($request->get('email'));
        $encodedPassword = $passwordHasher->hashPassword($user, $request->get('password'));
        $user->setPassword($encodedPassword);
        $roles = [];
        $roles[] = $request->get('role');
        $user->setRoles($roles);
        $user->setFirstName($request->get('firstname'));
        $user->setLastName($request->get('lastname'));
        $user->setBirthdate(new \DateTime($request->get('birthdate')));
        $user->setIsConfirmed(false);

        if ($request->get('role') == 'ROLE_PROGRAMMER')
        {
            $user->setLevel($request->get('level'));
            $user->setSpecialization($request->get('specialization'));
            $user->setTechnology($request->get('technology'));
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user, Response::HTTP_OK,[],[
            ObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::IGNORED_ATTRIBUTES => ['userIdentifier', 'username', 'Projects', 'Bugs', 'SubmittedBugs']
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
