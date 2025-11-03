<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Repository;

use BillaBear\Entity\CustomerRegistration;
use BillaBear\Repository\CustomerRegistrationRepository;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\CustomServiceRepository;
use PHPUnit\Framework\TestCase;

class CustomerRegistrationRepositoryTest extends TestCase
{
    public function testFindBySlugSuccess()
    {
        $slug = 'test-slug-123';
        $registration = $this->createMock(CustomerRegistration::class);

        $entityRepository = $this->createMock(CustomServiceRepository::class);
        $entityRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['slug' => $slug])
            ->willReturn($registration);

        $repository = new CustomerRegistrationRepository($entityRepository);

        $result = $repository->findBySlug($slug);

        $this->assertSame($registration, $result);
    }

    public function testFindBySlugNotFound()
    {
        $slug = 'non-existent-slug';

        $entityRepository = $this->createMock(CustomServiceRepository::class);
        $entityRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['slug' => $slug])
            ->willReturn(null);

        $repository = new CustomerRegistrationRepository($entityRepository);

        $this->expectException(NoEntityFoundException::class);
        $this->expectExceptionMessage("No customer registration found for slug 'non-existent-slug'");

        $repository->findBySlug($slug);
    }
}
