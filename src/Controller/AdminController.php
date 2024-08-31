<?php

namespace App\Controller;

use App\Service\TestResultService;
use App\Service\ExamineeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    public function __construct(
            private ExamineeService   $examineeService,
            private TestResultService $testResultService)
    {
    }

    #[Route('/admin/examinees', name: 'admin_examinees', methods: ['GET'])]
    public function adminExaminees(): Response
    {
        return $this->render("admin/examinee_list.html.twig", [
            'examinees' => $this->examineeService->getAllExaminees()
        ]);
    }


    #[Route('/admin/examinee_tests/{examineeId}', name: 'admin_examinee_tests', methods: ['GET'])]
    public function adminExamineeTests(int $examineeId): Response
    {
        return $this->render("admin/examinee_test_list.html.twig", [
            'examinee' => $this->examineeService->getExaminee($examineeId),
            'testResults' => $this->testResultService->getTestResults($examineeId)
        ]);
    }



    #[Route('/admin/test_result_answers/{testResultId}', name: 'admin_test_result_answers', methods: ['GET'])]
    public function adminTestResultAnswers(int $testResultId): Response
    {
        $testResultData = $this->testResultService->getTestResultData($testResultId);

        return $this->render("admin/test_result_answers.html.twig", $testResultData);
    }
}