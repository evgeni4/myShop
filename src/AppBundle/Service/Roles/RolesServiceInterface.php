<?php

namespace AppBundle\Service\Roles;
interface RolesServiceInterface
{
    public function findOneBy(string $criteria);
}