<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\CustomerRegistration;
use Parthenon\Athena\Repository\DoctrineCrudRepository;
use Parthenon\Common\Exception\NoEntityFoundException;

class CustomerRegistrationRepository extends DoctrineCrudRepository implements CustomerRegistrationRepositoryInterface
{
    /**
     * @throws NoEntityFoundException
     */
    public function findBySlug(string $slug): CustomerRegistration
    {
        $registration = $this->entityRepository->findOneBy(['slug' => $slug]);

        if (!$registration instanceof CustomerRegistration) {
            throw new NoEntityFoundException(sprintf("No customer registration found for slug '%s'", $slug));
        }

        return $registration;
    }
}
