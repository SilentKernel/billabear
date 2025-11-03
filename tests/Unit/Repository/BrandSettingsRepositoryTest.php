<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Tests\Unit\Repository;

use BillaBear\Entity\BrandSettings;
use BillaBear\Repository\BrandSettingsRepository;
use Parthenon\Common\Exception\NoEntityFoundException;
use Parthenon\Common\Repository\CustomServiceRepository;
use PHPUnit\Framework\TestCase;

class BrandSettingsRepositoryTest extends TestCase
{
    public function testGetDefaultBrandSettingsSuccess()
    {
        $brandSettings = $this->createMock(BrandSettings::class);

        $entityRepository = $this->createMock(CustomServiceRepository::class);
        $entityRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['isDefault' => true])
            ->willReturn($brandSettings);

        $repository = new BrandSettingsRepository($entityRepository);

        $result = $repository->getDefaultBrandSettings();

        $this->assertSame($brandSettings, $result);
    }

    public function testGetDefaultBrandSettingsFallbackToFirst()
    {
        $brandSettings1 = $this->createMock(BrandSettings::class);
        $brandSettings2 = $this->createMock(BrandSettings::class);

        $entityRepository = $this->createMock(CustomServiceRepository::class);
        $entityRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['isDefault' => true])
            ->willReturn(null);

        $entityRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$brandSettings1, $brandSettings2]);

        $repository = new BrandSettingsRepository($entityRepository);

        $result = $repository->getDefaultBrandSettings();

        $this->assertSame($brandSettings1, $result);
    }

    public function testGetDefaultBrandSettingsNoBrandsThrowsException()
    {
        $entityRepository = $this->createMock(CustomServiceRepository::class);
        $entityRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['isDefault' => true])
            ->willReturn(null);

        $entityRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $repository = new BrandSettingsRepository($entityRepository);

        $this->expectException(NoEntityFoundException::class);
        $this->expectExceptionMessage("No brand settings found");

        $repository->getDefaultBrandSettings();
    }
}
