<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Controller\Public;

use BillaBear\Customer\ExternalRegisterInterface;
use BillaBear\DataMappers\CustomerDataMapper;
use BillaBear\DataMappers\CustomerRegistrationDataMapper;
use BillaBear\Dto\Request\Public\CreateCustomerDto;
use BillaBear\Dto\Request\Public\Registration\CompleteRegistrationDto;
use BillaBear\Dto\Response\Portal\Quote\StripeInfo;
use BillaBear\Dto\Response\Portal\Registration\CustomerRegistered;
use BillaBear\Dto\Response\Portal\Registration\ViewRegistration;
use BillaBear\Repository\CustomerRegistrationRepositoryInterface;
use BillaBear\Repository\CustomerRepositoryInterface;
use BillaBear\Repository\SettingsRepositoryInterface;
use Parthenon\Billing\Config\FrontendConfig;
use Parthenon\Billing\PaymentMethod\FrontendAddProcessorInterface;
use Parthenon\Common\Exception\NoEntityFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class CustomerRegistrationController
{
    public function __construct(private LoggerInterface $controllerLogger)
    {
    }

    #[Route('/public/register/{slug}/view', name: 'app_public_registration_view', methods: ['GET'])]
    public function viewRegistration(
        Request $request,
        CustomerRegistrationRepositoryInterface $registrationRepository,
        CustomerRegistrationDataMapper $dataMapper,
        SerializerInterface $serializer,
    ) {
        $this->getLogger()->info('Received request to view registration', ['slug' => $request->get('slug')]);

        try {
            $registration = $registrationRepository->findBySlug($request->get('slug'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$registration->isValid()) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = $dataMapper->createPublicDto($registration);
        $viewDto = new ViewRegistration();
        $viewDto->setName($dto->getName());
        $json = $serializer->serialize($viewDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/public/register/{slug}/customer', name: 'app_public_registration_createcustomer', methods: ['POST'])]
    public function registerCustomer(
        Request $request,
        CustomerDataMapper $customerDataMapper,
        SerializerInterface $serializer,
        CustomerRepositoryInterface $customerRepository,
        CustomerRegistrationRepositoryInterface $registrationRepository,
        FrontendAddProcessorInterface $addCardByTokenDriver,
        ExternalRegisterInterface $externalRegister,
        SettingsRepositoryInterface $settingsRepository,
        FrontendConfig $config,
    ) {
        $this->getLogger()->info('Received request to register customer', ['slug' => $request->get('slug')]);

        try {
            $registration = $registrationRepository->findBySlug($request->get('slug'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$registration->isValid()) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        // Check if registration is for existing customer
        if ($registration->hasCustomer()) {
            // Use existing customer
            $customer = $registration->getCustomer();
        } else {
            // Create new customer
            /** @var CreateCustomerDto $input */
            $input = $serializer->deserialize($request->getContent(), CreateCustomerDto::class, 'json');
            $customer = $customerDataMapper->createCustomer($input);
            $customer->setBrand($registration->getBrandSettings()->getCode());
            $customer->setBrandSettings($registration->getBrandSettings());
            $externalRegister->register($customer);
            $customerRepository->save($customer);
        }

        $defaultSettings = $settingsRepository->getDefaultSettings();
        $apiKey = empty($config->getApiInfo()) ? $defaultSettings->getSystemSettings()->getStripePublicKey() : $config->getApiInfo();

        $stripe = new StripeInfo();
        $stripe->setToken($addCardByTokenDriver->startTokenProcess($customer));
        $stripe->setKey($apiKey);

        $responseDto = new CustomerRegistered();
        $responseDto->setCustomerId((string) $customer->getId());
        $responseDto->setStripe($stripe);

        $json = $serializer->serialize($responseDto, 'json');

        return new JsonResponse($json, json: true);
    }

    #[Route('/public/register/{slug}/payment-method', name: 'app_public_registration_addpaymentmethod', methods: ['POST'])]
    public function addPaymentMethod(
        Request $request,
        CustomerRegistrationRepositoryInterface $registrationRepository,
        CustomerRepositoryInterface $customerRepository,
        SerializerInterface $serializer,
        FrontendAddProcessorInterface $addCardByTokenDriver,
    ) {
        $this->getLogger()->info('Received request to add payment method', ['slug' => $request->get('slug')]);

        try {
            $registration = $registrationRepository->findBySlug($request->get('slug'));
        } catch (NoEntityFoundException $e) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$registration->isValid()) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }

        $inputDto = $serializer->deserialize($request->getContent(), CompleteRegistrationDto::class, 'json');
        $customer = $customerRepository->findById($inputDto->getCustomerId());

        // Create payment method from token (SetupIntent)
        $addCardByTokenDriver->createPaymentDetailsFromToken($customer, $inputDto->getToken());

        return new JsonResponse(['success' => true]);
    }

    private function getLogger(): LoggerInterface
    {
        return $this->controllerLogger;
    }
}
