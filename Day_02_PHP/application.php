#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setName("Day 02")
    ->addArgument('input', InputArgument::OPTIONAL, 'Specify input file, default: input.txt')
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {
            $getData = static function (string $filePath): array {
                $data = file_get_contents($filePath);
                return explode("\n", $data);
            };
            $findPasswordsCount = static function(array $data): int {
                $result = 0;
                foreach ($data as $item) {
                    [$rule, $pass] = explode(': ', $item);
                    [$range, $char] = explode(' ', $rule);
                    [$min, $max] = explode('-', $range);
                    preg_match_all(sprintf('/[%s]/',$char), $pass, $matches);
                    $count = count($matches[0]);
                    if ((int)$min <= $count && $count <= (int)$max) {
                        $result++;
                    }
                }
                return $result;
            };
            $findUpdatedPasswordsCount = static function(array $data): int {
                $result = 0;
                foreach ($data as $item) {
                    [$rule, $pass] = explode(': ', $item);
                    [$range, $char] = explode(' ', $rule);
                    [$min, $max] = explode('-', $range);
                    $charCount = 0;
                    $charCount += $pass[$min-1] === $char ? 1 : 0;
                    $charCount += $pass[$max-1] === $char ? 1 : 0;
                    if ($charCount === 1) {
                        $result++;
                    }
                }
                return $result;
            };

            ###

            $output->writeln('Advent of Code 2020 - Day 02');

            $filePath = $input->getArgument('input') ?? 'input.txt';

            $data = $getData($filePath);

            # Part 1
            $output->writeln('How many passwords are valid according to their policies?');
            $result = $findPasswordsCount($data);
            $output->writeln(sprintf('Result: %s', $result));

            # Part 2
            $output->writeln('How many passwords are valid according to the new interpretation of the policies?');
            $result = $findUpdatedPasswordsCount($data);
            $output->writeln(sprintf('Result: %s', $result));
        }
    )
    ->run();