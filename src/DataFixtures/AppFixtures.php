<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Persistence\ManagerRegistry;


class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
       $this->loadUsers($manager);
       $this->loadGroups($manager);

    }

    public function loadUsers($manager){
        $user = new User();
        $user->setUsername('admin');
        $user->setName('Jonas');
        $user->setRoles(['ROLE_ADMIN','ROLE_USER']);
        $password = $this->encoder->encodePassword($user, 'password');
        $user->setPassword($password);
        $manager->persist($user);

        $user1 = new User();
        $user1->setUsername('user');
        $user1->setName('Zigmas');
        $password = $this->encoder->encodePassword($user1, 'password');
        $user1->setPassword($password);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('test_user');
        $user2->setName('Petras');
        $password = $this->encoder->encodePassword($user2, 'password');
        $user2->setPassword($password);
        $manager->persist($user2);

        $manager->flush();
    }

    public function loadGroups($manager){

        $group = new Group();
        $group->setName('Programmers');
        $manager->persist($group);

        $group1 = new Group();
        $group1->setName('Drivers');
        $manager->persist($group1);

        $group2 = new Group();
        $group2->setName('Financiers');
        $manager->persist($group2);

        $group3 = new Group();
        $group3->setName('Managers');
        $manager->persist($group3);

        $manager->flush();
    }

}
