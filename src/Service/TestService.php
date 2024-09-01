<?php

namespace App\Service;

use App\Entity\TestResult;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class TestService
{
    public function __construct(
        private ExamineeService        $examineeService,
        private TestResultService      $testResultService,
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @throws Exception
     */
    public function saveTest(array $requestParams, ?int $examineeId = null) :TestResult
    {
        if(empty($requestParams['testResult'])) {
            throw new Exception("testResult data missing");
        }

        if(!empty($examineeId)) {
            $examinee = $this->examineeService->getExaminee($examineeId);
        }

        try {
            $this->entityManager->beginTransaction();

            if(empty($examinee)) {
                if(empty($requestParams['examinee'])) {
                    throw new Exception("examinee data missing");
                }
                $examinee = $this->examineeService->saveExaminee($requestParams['examinee']);
            }

            $testResult = $this->testResultService->saveTestResult($requestParams["testResult"], $examinee);

            $this->entityManager->commit();

        } catch (Exception $exception) {
            $this->entityManager->rollback();

            throw new Exception($exception->getMessage());
        }

        return $testResult;
    }
}