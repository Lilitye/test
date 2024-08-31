<?php

namespace App\Service;

use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;

class TestQuestionService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {

    }

    public function getTestQuestions() :array
    {
        return $this->entityManager->getRepository(Question::class)->findAll();
    }
}