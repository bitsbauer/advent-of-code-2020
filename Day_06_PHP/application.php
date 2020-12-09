#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setName("Day6")
    ->addArgument('input', InputArgument::OPTIONAL, 'Specify input file')
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $output->writeln('Advent of Code 2020 - Day 6');

        $readInputFile = static function(InputInterface $input): string {
            $inputArg = $input->getArgument('input');
            return $inputArg ?? 'input.txt';
        };

        $getGroupsFromData = static function(string $filePath): array {
            $data = file_get_contents($filePath);
            return explode("\n\n", $data);
        };

        $getGroupAnswers = static function(string $group): array {
            $persons = explode("\n", $group);
            $groupAnswers['persons'] = count($persons);
            foreach ($persons as $answers) {
                foreach (str_split($answers) as $answer) {
                    $groupAnswers['answers'][$answer] = isset($groupAnswers['answers'][$answer])
                        ? ++$groupAnswers['answers'][$answer]
                        : 1;
                }
            }
            return $groupAnswers;
        };

        $filePath = $readInputFile($input);
        $groups = $getGroupsFromData($filePath);

        # Part 1
        $output->writeln('For each group, count the number of questions to which anyone answered "yes". What is the sum of those counts?');
        $count = 0;
        foreach ($groups as $group) {
            $count += count($getGroupAnswers($group)['answers']);
        }
        $output->writeln(sprintf('Result: %s', $count));

        # Part 2
        $output->writeln('For each group, count the number of questions to which everyone answered "yes". What is the sum of those counts?');
        $count = 0;
        foreach ($groups as $group) {
            $groupAnswers = $getGroupAnswers($group);
            foreach ($groupAnswers['answers'] as $answerCount) {
                if ($groupAnswers['persons'] === $answerCount) {
                    ++$count;
                }
            }
        }
        $output->writeln(sprintf('Result: %s', $count));
    })
    ->run();