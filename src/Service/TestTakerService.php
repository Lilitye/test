<?php

namespace App\Service;

use App\Entity\TestResult;
use App\Entity\TestTaker;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use DateTimeImmutable;

class TestTakerService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getTestTaker(int $id) :?TestTaker
    {
        return $this->entityManager->getRepository(TestTaker::class)->find($id);
    }

    public function saveTestTaker(array $testTakerData) :TestTaker
    {
        $this->validate($testTakerData);

        $testTaker = new TestTaker();
        $testTaker->setFirstName($testTakerData['firstName']);
        $testTaker->setLastName($testTakerData['lastName']);
        $testTaker->setCreatedAt(new DateTimeImmutable());

        $this->entityManager->persist($testTaker);
        $this->entityManager->flush();

        return $testTaker;
    }
    public function getAllTestTakers() :array
    {
        return $this->entityManager->getRepository(TestTaker::class)->findBy([], ['createdAt' => 'DESC']);
    }

    private function validate(array $testTakerData) :void
    {
        if(empty($testTakerData['firstName'])) {
            throw new Exception("First name is required");
        }
        if(empty($testTakerData['lastName'])) {
            throw new Exception("Last name is required");
        }
    }

}