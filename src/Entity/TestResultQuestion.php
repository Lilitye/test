<?php

namespace App\Entity;

use App\Repository\TestResultQuestionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestResultQuestionRepository::class)]
class TestResultQuestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'testResultQuestions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TestResult $testResult = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    #[ORM\Column]
    private ?bool $isCorrectResult = null;

    #[ORM\Column(nullable: true)]
    private ?array $answerIds = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTestResult(): ?TestResult
    {
        return $this->testResult;
    }

    public function setTestResult(?TestResult $testResult): static
    {
        $this->testResult = $testResult;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getIsCorrectResult(): ?bool
    {
        return $this->isCorrectResult;
    }

    public function setIsCorrectResult(bool $isCorrectResult): static
    {
        $this->isCorrectResult = $isCorrectResult;

        return $this;
    }

    public function getAnswerIds(): ?array
    {
        return $this->answerIds;
    }

    public function setAnswerIds(?array $answerIds): static
    {
        $this->answerIds = $answerIds;

        return $this;
    }
}
