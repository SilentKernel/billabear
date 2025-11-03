<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Registration;

use BillaBear\Dto\Request\Api\CreateCustomerRegistrationDto;
use BillaBear\Entity\CustomerRegistration;
use BillaBear\Repository\BrandSettingsRepositoryInterface;
use BillaBear\Repository\CustomerRegistrationRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class CustomerRegistrationCreator
{
    public function __construct(
        private CustomerRegistrationRepositoryInterface $customerRegistrationRepository,
        private BrandSettingsRepositoryInterface $brandSettingsRepository,
        private Security $security,
    ) {
    }

    public function createRegistration(CreateCustomerRegistrationDto $dto): CustomerRegistration
    {
        $registration = new CustomerRegistration();
        $registration->setName($dto->getName());
        $registration->setPermanent($dto->isPermanent());
        $registration->setSlug(bin2hex(random_bytes(48)));
        $registration->setValid(true);

        $user = $this->security->getUser();
        if ($user) {
            $registration->setCreatedBy($user);
        }

        // Get the default brand settings - in a real app, this might be passed via DTO
        $brandSettings = $this->brandSettingsRepository->getDefaultBrandSettings();
        $registration->setBrandSettings($brandSettings);

        $registration->setCreatedAt(new \DateTime());
        $registration->setUpdatedAt(new \DateTime());

        $this->customerRegistrationRepository->save($registration);

        return $registration;
    }
}
