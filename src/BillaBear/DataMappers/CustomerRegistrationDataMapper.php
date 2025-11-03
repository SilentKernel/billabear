<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\DataMappers;

use BillaBear\Dto\Response\Api\CustomerRegistrationView as ApiDto;
use BillaBear\Dto\Response\Portal\Registration\ViewRegistration as PublicDto;
use BillaBear\Entity\CustomerRegistration as Entity;

class CustomerRegistrationDataMapper
{
    public function createApiDto(Entity $entity): ApiDto
    {
        $dto = new ApiDto();
        $dto->setId((string) $entity->getId());
        $dto->setName($entity->getName());
        $dto->setSlug($entity->getSlug());
        $dto->setRegistrationUrl('/portal/register/'.$entity->getSlug());
        $dto->setPermanent($entity->isPermanent());
        $dto->setValid($entity->isValid());
        $dto->setCreatedAt($entity->getCreatedAt());

        return $dto;
    }

    public function createPublicDto(Entity $entity): PublicDto
    {
        $dto = new PublicDto();
        $dto->setName($entity->getName());

        return $dto;
    }
}
