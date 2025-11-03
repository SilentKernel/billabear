<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Api;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\CustomerRegistrationDataMapper;
use BillaBear\Dto\Request\Api\CreateCustomerRegistrationDto;
use BillaBear\Registration\CustomerRegistrationCreator;
use BillaBear\Repository\CustomerRegistrationRepositoryInterface;
use Parthenon\Athena\ResultSet;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerRegistrationController
{
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/api/v1/customer-registration', name: 'app_api_customerregistration_create', methods: ['POST'])]
    public function createRegistration(
        Request $request,
        CustomerRegistrationCreator $creator,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CustomerRegistrationDataMapper $dataMapper,
    ): Response {
        $this->getLogger()->info('Received an API request to create a customer registration link');

        /** @var CreateCustomerRegistrationDto $dto */
        $dto = $serializer->deserialize($request->getContent(), CreateCustomerRegistrationDto::class, 'json');
        $errors = $validator->validate($dto);
        $response = $this->handleErrors($errors);

        if ($response) {
            return $response;
        }

        $registration = $creator->createRegistration($dto);
        $responseDto = $dataMapper->createApiDto($registration);
        $json = $serializer->serialize($responseDto, 'json');

        return new JsonResponse($json, status: Response::HTTP_CREATED, json: true);
    }

    #[Route('/api/v1/customer-registration', name: 'app_api_customerregistration_list', methods: ['GET'])]
    public function listRegistrations(
        Request $request,
        CustomerRegistrationRepositoryInterface $repository,
        CustomerRegistrationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ): Response {
        $this->getLogger()->info('Received an API request to list customer registrations');

        $lastId = $request->get('last_id');
        $limit = (int) $request->get('limit', 10);

        if ($limit < 1) {
            return new JsonResponse(['error' => 'Limit must be greater than 0'], Response::HTTP_BAD_REQUEST);
        }

        if ($limit > 100) {
            $limit = 100;
        }

        $resultSet = new ResultSet();
        $resultSet->setResults(array_map([$dataMapper, 'createApiDto'], $repository->getList(limit: $limit, lastId: $lastId)->getResults()));

        $json = $serializer->serialize($resultSet, 'json');

        return new JsonResponse($json, json: true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
