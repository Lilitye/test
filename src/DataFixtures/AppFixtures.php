<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Service\InsertDBDataService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class AppFixtures extends Fixture
{
    const DATA_JSON_NAME = 'data.json';

    public function load(ObjectManager $manager): void
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . self::DATA_JSON_NAME;

        if(!file_exists($filePath)) {
            throw new Exception(self::DATA_JSON_NAME.' file not found to insert data');
        }

        $data = file_get_contents($filePath);

        if($data === false) {
            throw new Exception(self::DATA_JSON_NAME.' file is not readable');
        }

        if(json_validate($data) === false) {
            throw new Exception('Invalid JSON in '.self::DATA_JSON_NAME);
        }

        $insertData = json_decode($data, true);

        $this->validateData($insertData);

        foreach ($insertData as $dataItem) {
            $question = new Question();
            $question->setQuestionText($dataItem['questionText']);

            $manager->persist($question);

            foreach ($dataItem['answers'] as $answerItem) {
                $answer = new Answer();
                $answer->setAnswerText($answerItem['answerText']);
                $answer->setIsCorrect($answerItem['isCorrect']);
                $answer->setQuestion($question);

                $manager->persist($answer);
            }
        }

        $manager->flush();
    }

    private function validateData($data) :void
    {
        $errors = [];

        foreach ($data as $key => $dataItem) {
            if(empty($dataItem['questionText'])) {
                $errors[] = "'questionText' missing in question $key";
            }
            if(empty($dataItem['answers'])) {
                $errors[] = "'answers' missing in question $key";
            }

            foreach ($dataItem['answers'] as $keyAnswer => $answerItem) {
                if(!isset($answerItem['answerText'])) {
                    $errors[] = "'answerText' missing in question $key, answer $keyAnswer";
                }
                if(!isset($answerItem['isCorrect'])) {
                    $errors[] = "'isCorrect' missing in question $key, answer $keyAnswer";
                }
            }
        }

        if(!empty($errors)) {
            throw new Exception(implode("\n", $errors));
        }
    }
}
