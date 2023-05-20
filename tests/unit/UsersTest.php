<?php

namespace App\Tests\unit;

use App\Entity\Users;
use PHPUnit\Framework\TestCase;

class UsersTest extends TestCase
{
    public function testUser()
    {
        $user = new Users;
        $user->setRoles(['ROLE_USER']);
        $user->setEmail('test@gmail.com');
        $user->setPassword('azerty');
        $user->setLastname('Dupont');
        $user->setFirstname('Jean');
        $user->setAdress('rue des fleurs');
        $user->setZipcode('75000');
        $user->setCity('Paris');
        $user->setIs_verified(true);
        $user->setResetToken('');

        $this->assertSame(['ROLE_USER'], $user->getRoles());
        $this->assertSame('test@gmail.com', $user->getEmail());
        $this->assertSame('azerty', $user->getPassword());
        $this->assertSame('Dupont', $user->getLastname());
        $this->assertSame('Jean', $user->getFirstname());
        $this->assertSame('rue des fleurs', $user->getAdress());
        $this->assertSame('75000', $user->getZipcode());
        $this->assertSame('Paris', $user->getCity());
        $this->assertSame(true, $user->getIs_verified());
        $this->assertSame('', $user->getResetToken());
    }
}
