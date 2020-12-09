#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setName("Day 03")
    ->addArgument('input', InputArgument::OPTIONAL, 'Specify input file, default: input.txt')
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {
            $getData = static function (string $filePath): array {
                $data = file_get_contents($filePath);
                return explode("\n", trim($data));
            };
            $countTreesOnSlope = static function (array $data, int $right, int $down) {
                $treeCount = 0;
                $x = -$right;
                $y = 0;
                foreach ($data as $line) {
                    if ($y++ % $down !== 0) {
                        continue;
                    }
                    $x += $right;
                    $field = $line[$x % 31];
                    if ($field === '#') {
                        $treeCount++;
                    }
                }
                return $treeCount;
            };
            $multiplySlopeCount = static function($data, $slopes) use ($countTreesOnSlope) {
                $multiply = 1;
                foreach ($slopes as $slope) {
                    [$right, $down] = $slope;
                    $treeCount = $countTreesOnSlope($data, $right, $down);
                    $multiply *= $treeCount;
                }
                return $multiply;
            };

            ###

            $output->writeln('Advent of Code 2020 - Day 03');

            $filePath = $input->getArgument('input') ?? 'input.txt';

            $data = $getData($filePath);

            # Part 1
            $output->writeln('Starting at the top-left corner of your map and following a slope of right 3 and down 1, how many trees would you encounter?');
            $result = $countTreesOnSlope($data, 3, 1);
            $output->writeln(sprintf('Result: %s', $result));

            # Part 2
            $output->writeln('What do you get if you multiply together the number of trees encountered on each of the listed slopes?');
            $slopes = [[1, 1], [3, 1], [5, 1], [7, 1], [1, 2]];
            $result = $multiplySlopeCount($data, $slopes);
            $output->writeln(sprintf('Result: %s', $result));
        }
    )
    ->run();