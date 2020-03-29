<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;

class UserFixture extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $zampe = new User();
        $zampe->setUsername('zampe');
        $zampe->setPassword('zampe_password');
        $zampe->setPassword($this->passwordEncoder->encodePassword($zampe,'zampe_password'));
        $zampe->setFirstName('Ignacio');
        $zampe->setLastName('Zampelunghe');
        $manager->persist($zampe);

        $gab = new User();
        $gab->setUsername('gab');
        $gab->setPassword('gab_password');
        $gab->setPassword($this->passwordEncoder->encodePassword($gab,'gab_password'));
        $gab->setFirstName('Gabriel');
        $gab->setLastName('Brañeiro');
        $manager->persist($gab);

        $manager->flush();

        //$x = $this->passwordEncoder->isPasswordValid($gab,"gab_passwsord");
        //dd($x);
    }
}
