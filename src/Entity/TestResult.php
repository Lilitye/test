<?php

namespace App\Entity;

use App\Repository\TestResultRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestResultRepository::class)]
class TestResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TestTaker $testTaker = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeInterface $testTakenAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $percent = null;

    /**
     * @var Collection<int, TestResultQuestion>
     */
    #[ORM\OneToMany(targetEntity: TestResultQuestion::class, mappedBy: 'testResult')]
    private Collection $testResultQuestions;

    public function __construct()
    {
        $this->testResultQuestions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTestTaker(): ?TestTaker
    {
        return $this->testTaker;
    }

    public function setTestTaker(?TestTaker $testTaker): static
    {
        $this->testTaker = $testTaker;

        return $this;
    }

    public function getTestTakenAt(): ?\DateTimeInterface
    {
        return $this->testTakenAt;
    }

    public function setTestTakenAt(\DateTimeInterface $testTakenAt): static
    {
        $this->testTakenAt = $testTakenAt;

        return $this;
    }

    public function getPercent(): ?string
    {
        return $this->percent;
    }

    public function setPercent(string $percent): static
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * @return Collection<int, TestResultQuestion>
     */
    public function getTestResultQuestions(): Collection
    {
        return $this->testResultQuestions;
    }

    public function addTestResultQuestion(TestResultQuestion $testResultQuestion): static
    {
        if (!$this->testResultQuestions->contains($testResultQuestion)) {
            $this->testResultQuestions->add($testResultQuestion);
            $testResultQuestion->setTestResult($this);
        }

        return $this;
    }

    public function removeTestResultQuestion(TestResultQuestion $testResultQuestion): static
    {
        if ($this->testResultQuestions->removeElement($testResultQuestion)) {
            // set the owning side to null (unless already changed)
            if ($testResultQuestion->getTestResult() === $this) {
                $testResultQuestion->setTestResult(null);
            }
        }

        return $this;
    }
}
