#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setName("Day9")
    ->addArgument('input', InputArgument::OPTIONAL, 'Specify input file, default: input.txt')
    ->addArgument('preamble', InputArgument::OPTIONAL, 'Specify preamble value, default: 25')
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {
            $getData = static function (string $filePath): array {
                $data = file_get_contents($filePath);
                return explode("\n", $data);
            };
            $hasDataSliceSumExpectedValue = static function (array $dataSlice, string $expected): bool {
                sort($dataSlice);
                $count = count($dataSlice);
                foreach ($dataSlice as $i => $value) {
                    for ($j = $i + 1; $j < $count; $j++) {
                        $sum = gmp_add($value, $dataSlice[$j]);
                        $compare = gmp_cmp($sum, $expected);
                        if ($compare > 0) {
                            continue;
                        }
                        if ($compare === 0) {
                            return true;
                        }
                    }
                }
                return false;
            };
            $findInvalidPreambleSum = static function (array $data, int $preamble) use ($hasDataSliceSumExpectedValue
            ): string {
                $index = -1;
                do {
                    $dataSlice = array_slice($data, ++$index, $preamble + 1);
                    $expected = array_pop($dataSlice);
                } while ($hasDataSliceSumExpectedValue($dataSlice, $expected));
                return $expected;
            };
            $findContiguousRange = static function (array $data, string $expectedSum): array {
                $count = count($data);
                for ($i = 0; $i < $count; $i++) {
                    $sum = "0";
                    $compare = -1;
                    for ($j = $i; $compare < 0; $j++) {
                        $result = gmp_add($sum, $data[$j]);
                        $sum = gmp_strval($result);
                        $compare = gmp_cmp($sum, $expectedSum);
                        if ($compare === 0) {
                            return array_slice($data, $i, $j - $i + 1);
                        }
                    }
                }
                throw new RuntimeException('Could not find contiguous range');
            };
            $findLowHighSumFromRange = static function (array $range): string {
                sort($range);
                return gmp_strval(gmp_add(array_shift($range), array_pop($range)));
            };

            ###

            $output->writeln('Advent of Code 2020 - Day 9');

            $filePath = $input->getArgument('input') ?? 'input.txt';
            $preamble = $input->getArgument('preamble') ?? '25';

            $data = $getData($filePath);

            # Part 1
            $output->writeln('What is the first number that does not have this property?');
            $invalidSum = $findInvalidPreambleSum($data, (int)$preamble);
            $output->writeln(sprintf('Result: %s', $invalidSum));

            # Part 2
            $output->writeln('What is the encryption weakness in your XMAS-encrypted list of numbers?');
            $contiguousRange = $findContiguousRange($data, $invalidSum);
            $highLowRangeSum = $findLowHighSumFromRange($contiguousRange);
            $output->writeln(sprintf('Result: %s', $highLowRangeSum));
        }
    )
    ->run();