<?php

namespace App\Controller;

use App\Service\TestResultService;
use App\Service\TestQuestionService;
use App\Service\TestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Exception;
use Psr\Log\LoggerInterface;

class TestController extends AbstractController
{

    public function __construct(
        private TestQuestionService   $testQuestionService,
        private TestService           $testService,
        private TestResultService     $testResultService,
        private LoggerInterface       $logger
    )
    {
    }

    #[Route('/', name: 'init_test',  methods: ['POST', 'GET'])]
    public function initTest(Request $request): Response
    {
        $requestParams = $request->request->all();
        $session = $request->getSession();

        if(!empty($requestParams)) {
            try {
                $examineeSession = $session->get('examinee');

                $testResult = $this->testService->saveTest($requestParams, $examineeSession['id'] ?? null);

                $examinee = $testResult->getExaminee();
                $session->set('examinee', [
                    'id' => $examinee->getId(),
                    'firstName' => $examinee->getFirstName(),
                    'lastName' => $examinee->getLastName()
                ]);

                return $this->redirectToRoute('test_result', ['id' => $testResult->getId()]);

            } catch (Exception $exception) {
                $this->logger->error("Fail to save test results, error message: {$exception->getMessage()}");

                return $this->render("test/error_message.html.twig", ['errorMessage' => 'Fail to save test results']);
            }
        }

        $params = [
            'questions' => $this->testQuestionService->getTestQuestions(),
        ];

        if(!empty($session->get('examinee'))) {
            $params['examinee'] = $session->get('examinee');
        }

        return $this->render('test/test_wizard.html.twig', $params);
    }

    #[Route('/test_result/{id}', name: 'test_result', methods: ['GET'])]
    public function initTestResult(int $id, Request $request): Response
    {
        try {
            $testResultData = $this->testResultService->getTestResultData($id);

            $params = [
                'testResultPercent' => $testResultData['testResult']->getPercent(),
                'correctResult' => $testResultData['correctResult'],
                'wrongResult' => $testResultData['wrongResult']
            ];

            $examinee = $request->getSession()->get('examinee');
            if(!empty($examinee)) {
                $params['examinee'] = $examinee;
            }

            return $this->render("test/test_result.html.twig", $params);

        } catch (Exception $exception) {
            $this->logger->error("Fail to get test results, error message: {$exception->getMessage()}");

            return $this->render("test/error_message.html.twig", ['errorMessage' => 'Test result not found']);
        }
    }

}