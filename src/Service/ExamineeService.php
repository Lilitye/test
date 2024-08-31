<?php

namespace App\Service;

use App\Entity\TestResult;
use App\Entity\Examinee;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use DateTimeImmutable;

class ExamineeService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getExaminee(int $id) :?Examinee
    {
        return $this->entityManager->getRepository(Examinee::class)->find($id);
    }

    public function saveExaminee(array $examineeData) :Examinee
    {
        $this->validate($examineeData);

        $examinee = new Examinee();
        $examinee->setFirstName($examineeData['firstName']);
        $examinee->setLastName($examineeData['lastName']);
        $examinee->setCreatedAt(new DateTimeImmutable());

        $this->entityManager->persist($examinee);
        $this->entityManager->flush();

        return $examinee;
    }
    public function getAllExaminees() :array
    {
        return $this->entityManager->getRepository(Examinee::class)->findBy([], ['createdAt' => 'DESC']);
    }

    private function validate(array $examineeData) :void
    {
        if(empty($examineeData['firstName'])) {
            throw new Exception("First name is required");
        }
        if(empty($examineeData['lastName'])) {
            throw new Exception("Last name is required");
        }
    }

}