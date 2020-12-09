#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setName("Day 01")
    ->addArgument('input', InputArgument::OPTIONAL, 'Specify input file, default: input.txt')
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {
            $getData = static function (string $filePath): array {
                $data = file_get_contents($filePath);
                return explode("\n", $data);
            };
            $twoNumberSumMult = static function(array $data): int {
                foreach ($data as $first) {
                    foreach ($data as $second) {
                        if ($first+$second === 2020) {
                            return $first*$second;
                        }
                    }
                }
            };
            $threeNumberSumMult = static function(array $data): int {
                foreach ($data as $first) {
                    foreach ($data as $second) {
                        foreach ($data as $third) {
                            if ($first+$second+$third === 2020) {
                                return $first*$second*$third;
                            }
                        }
                    }
                }
            };

            ###

            $output->writeln('Advent of Code 2020 - Day 01');

            $filePath = $input->getArgument('input') ?? 'input.txt';

            $data = $getData($filePath);

            # Part 1
            $output->writeln('Find the two entries that sum to 2020; what do you get if you multiply them together?');
            $result = $twoNumberSumMult($data);
            $output->writeln(sprintf('Result: %s', $result));

            # Part 2
            $output->writeln('In your expense report, what is the product of the three entries that sum to 2020?');
            $result = $threeNumberSumMult($data);
            $output->writeln(sprintf('Result: %s', $result));
        }
    )
    ->run();