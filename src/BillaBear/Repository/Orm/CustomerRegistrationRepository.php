<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository\Orm;

use BillaBear\Entity\CustomerRegistration;
use Doctrine\Persistence\ManagerRegistry;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\CustomServiceRepository;

class CustomerRegistrationRepository extends CustomServiceRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerRegistration::class);
    }

    /**
     * @throws NoEntityFoundException
     */
    public function findBySlug(string $slug): CustomerRegistration
    {
        $registration = $this->findOneBy(['slug' => $slug]);

        if (!$registration instanceof CustomerRegistration) {
            throw new NoEntityFoundException(sprintf("No customer registration found for slug '%s'", $slug));
        }

        return $registration;
    }
}
