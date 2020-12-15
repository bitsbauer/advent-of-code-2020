#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setName("Day 15: Rambunctious Recitation")
    ->addArgument('input', InputArgument::OPTIONAL, 'Specify input file, default: input.txt')
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {
            $getStartingNumbers = static function (string $filePath): array {
                return array_map(
                    static function (string $number) {
                        return (int)$number;
                    },
                    explode(',', file_get_contents($filePath))
                );
            };

            $getSpoken = static function (array &$numbers, int $lastSpoken, int $turn): int {
                if ($numbers[$lastSpoken]['prevTurn'] === null) {
                    $spoken = 0;
                } else {
                    $spoken = $numbers[$lastSpoken]['turn'] - $numbers[$lastSpoken]['prevTurn'];
                }
                $prevTurn = isset($numbers[$spoken]) ? $numbers[$spoken]['turn'] : null;
                $numbers[$spoken] = [
                    'turn' => $turn,
                    'prevTurn' => $prevTurn,
                ];
                return $spoken;
            };

            $play = static function (array $startingNumbers, int $maxTurn) use ($getSpoken): int {
                $turn = 0;
                $lastSpoken = null;
                $numbers = [];

                foreach ($startingNumbers as $startingNumber) {
                    $turn++;
                    $lastSpoken = $startingNumber;
                    $numbers[$lastSpoken] = [
                        'turn' => $turn,
                        'prevTurn' => null
                    ];
                }
                while ($turn < $maxTurn) {
                    $turn++;
                    $lastSpoken = $getSpoken($numbers, $lastSpoken, $turn);
                }
                return $lastSpoken;
            };

            ###

            $output->writeln('Advent of Code 2020 - Day 15: Rambunctious Recitation');

            if (PHP_INT_SIZE !== 8) {
                throw new RuntimeException('Sorry, 64 Bit OS required...');
            }

            $filePath = $input->getArgument('input') ?? 'input.txt';
            $startingNumbers = $getStartingNumbers($filePath);

            # Part 1
            $output->writeln('Given your starting numbers, what will be the 2020th number spoken?');
            $result = $play($startingNumbers, 2020);
            $output->writeln(sprintf('Result: %s', $result));

            # Part 2
            $output->writeln('Given your starting numbers, what will be the 30000000th number spoken?');
            $result = $play($startingNumbers, 30000000);
            $output->writeln(sprintf('Result: %s', $result));
        }
    )
    ->run();