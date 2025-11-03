<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use BillaBear\Entity\CustomerRegistration;
use Parthenon\Athena\Repository\CrudRepositoryInterface;

/**
 * @method getById($id, $includeDeleted = false) CustomerRegistration
 * @method findById($id) CustomerRegistration
 */
interface CustomerRegistrationRepositoryInterface extends CrudRepositoryInterface
{
    public function findBySlug(string $slug): CustomerRegistration;
}
