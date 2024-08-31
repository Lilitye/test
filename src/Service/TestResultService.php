<?php

namespace App\Service;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\TestResult;
use App\Entity\TestResultQuestion;
use App\Entity\Examinee;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use DateTimeImmutable;

class TestResultService
{

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getTestResultData(int $id) :array
    {
        $testResult = $this->entityManager->getRepository(TestResult::class)->find($id);

        if(empty($testResult)) {
            throw new Exception("TestResult not found");
        }

        $correctResult = [];
        $wrongResult = [];

        /** @var TestResultQuestion $testResultQuestion */
        foreach ($testResult->getTestResultQuestions() as $testResultQuestion) {
            if($testResultQuestion->getIsCorrectResult()) {
                $correctResult[] = $testResultQuestion;
            } else {
                $wrongResult[] = $testResultQuestion;
            }
        }

        return [
            'testResult' => $testResult,
            'correctResult' => $correctResult,
            'wrongResult' => $wrongResult
        ];
    }


    public function saveTestResult(array $testResultData, Examinee $examinee) :TestResult
    {
        $questions = $this->entityManager->getRepository(Question::class)->createQueryBuilder('question')
            ->leftJoin('question.answers', 'answer')
            ->addSelect('answer')
            ->getQuery()
            ->getResult();

        $questionsById = [];
        foreach ($questions as $question) {
            $questionsById[$question->getId()] = $question;
        }

        $isCorrectResult = $this->checkTestQuestionResult($questionsById, $testResultData);

        $allQuestionsCount = count($questionsById);
        $percent = !empty($allQuestionsCount) ? count(array_filter($isCorrectResult)) * 100 / count($testResultData) : 0;
        $percent = round($percent, 2);

        $testResult = new TestResult();
        $testResult->setExaminee($examinee);
        $testResult->setPercent($percent);
        $testResult->setTestTakenAt(new DateTimeImmutable());

        $this->entityManager->persist($testResult);

        foreach ($testResultData as $questionId => $answerIds) {
            $testResultQuestion = new TestResultQuestion();
            $testResultQuestion->setTestResult($testResult);
            $testResultQuestion->setQuestion($questionsById[$questionId]);
            $testResultQuestion->setAnswerIds($answerIds);

            $testResultQuestion->setIsCorrectResult($isCorrectResult[$questionId]);

            $this->entityManager->persist($testResultQuestion);
        }

        $this->entityManager->flush();

        return $testResult;
    }

    public function getTestResults(?int $examineeId) :array
    {
        $params = !empty($examineeId) ? ['examinee' => $examineeId] : [];

        return $this->entityManager->getRepository(TestResult::class)->findBy($params, ['testTakenAt' => 'DESC']);
    }

    private function checkTestQuestionResult(array $questions, array $testResultData) :array
    {
        $isCorrectResult = [];

        foreach ($testResultData as $questionId => $resultAnswerIds) {
            if(empty($questions[$questionId])) {
                throw new Exception("Question $questionId not found");
            }
            $question = $questions[$questionId];

            $answers = $question->getAnswers();

            $correctAnswerIds = [];
            /** @var Answer $answer */
            foreach ($answers as $answer) {
                if($answer->getIsCorrect()) {
                    $correctAnswerIds[] = $answer->getId();
                }
            }

            $isCorrectResult[$questionId] = empty(array_diff($resultAnswerIds, $correctAnswerIds));
        }

        return $isCorrectResult;
    }
}