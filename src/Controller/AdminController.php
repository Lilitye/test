<?php

namespace App\Controller;

use App\Service\TestResultService;
use App\Service\TestTakerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    public function __construct(
            private TestTakerService $testTakerService,
            private TestResultService $testResultService)
    {
    }

    #[Route('/admin/test_takers', name: 'admin_test_takers', methods: ['GET'])]
    public function adminTestTakers(): Response
    {
        return $this->render("admin/test_taker_list.html.twig", [
            'testTakers' => $this->testTakerService->getAllTestTakers()
        ]);
    }


    #[Route('/admin/test_taker_tests/{testTakerId}', name: 'admin_test_taker_tests', methods: ['GET'])]
    public function adminTestTakerTests(int $testTakerId): Response
    {
        return $this->render("admin/test_taker_test_list.html.twig", [
            'testTaker' => $this->testTakerService->getTestTaker($testTakerId),
            'testResults' => $this->testResultService->getTestResults($testTakerId)
        ]);
    }



    #[Route('/admin/test_result_answers/{testResultId}', name: 'admin_test_result_answers', methods: ['GET'])]
    public function adminTestResultAnswers(int $testResultId): Response
    {
        $testResultData = $this->testResultService->getTestResultData($testResultId);

        return $this->render("admin/test_result_answers.html.twig", $testResultData);
    }
}