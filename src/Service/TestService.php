<?php

namespace App\Service;

use App\Entity\TestResult;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TestService
{
    public function __construct(
        private TestTakerService       $testTakerService,
        private TestResultService      $testResultService,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function saveTestSession(array $requestParams, ?int $testTakerId = null) :TestResult
    {
        if(empty($requestParams['testResult'])) {
            throw new Exception("testResult data missing");
        }

        if(!empty($testTakerId)) {
            $testTaker = $this->testTakerService->getTestTaker($testTakerId);
        }

        try {
            $this->entityManager->beginTransaction();

            if(empty($testTaker)) {
                if(empty($requestParams['testTaker'])) {
                    throw new Exception("testTaker data missing");
                }
                $testTaker = $this->testTakerService->saveTestTaker($requestParams['testTaker']);
            }

            $testResult = $this->testResultService->saveTestResult($requestParams["testResult"], $testTaker);

            $this->entityManager->commit();

        } catch (Exception $exception) {
            $this->entityManager->rollback();

            throw new Exception($exception->getMessage());
        }

        return $testResult;
    }
}