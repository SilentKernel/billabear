<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\App;

use BillaBear\Controller\ValidationErrorResponseTrait;
use BillaBear\DataMappers\CustomerRegistrationDataMapper;
use BillaBear\Dto\Request\Api\CreateCustomerRegistrationDto;
use BillaBear\Registration\CustomerRegistrationCreator;
use BillaBear\Repository\CustomerRegistrationRepositoryInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerRegistrationController
{
    use CrudListTrait;
    use ValidationErrorResponseTrait;

    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/app/registration', name: 'app_app_registration_list', methods: ['GET'])]
    public function listRegistrations(
        Request $request,
        CustomerRegistrationRepositoryInterface $repository,
        SerializerInterface $serializer,
        CustomerRegistrationDataMapper $dataMapper,
    ): Response {
        $this->getLogger()->info('Received request to list customer registrations');

        return $this->crudList($request, $repository, $serializer, $dataMapper);
    }

    #[IsGranted('ROLE_ACCOUNT_MANAGER')]
    #[Route('/app/registration', name: 'app_app_registration_create', methods: ['POST'])]
    public function createRegistration(
        Request $request,
        CustomerRegistrationCreator $creator,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CustomerRegistrationDataMapper $dataMapper,
    ): Response {
        $this->getLogger()->info('Received request to create customer registration');

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

    #[Route('/app/registration/{id}', name: 'app_app_registration_view', methods: ['GET'])]
    public function viewRegistration(
        Request $request,
        CustomerRegistrationRepositoryInterface $repository,
        SerializerInterface $serializer,
        CustomerRegistrationDataMapper $dataMapper,
    ): Response {
        $this->getLogger()->info('Received request to view customer registration', ['id' => $request->get('id')]);

        try {
            $registration = $repository->findById($request->get('id'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $dto = $dataMapper->createApiDto($registration);
        $json = $serializer->serialize($dto, 'json');

        return new JsonResponse($json, json: true);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
