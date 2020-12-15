#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setName("Day 14: Docking Data")
    ->addArgument('input', InputArgument::OPTIONAL, 'Specify input file, default: input.txt')
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {

            $getData = static function (string $filePath): array {
                $data = file_get_contents($filePath);
                $data = explode("\n", $data);
                $operations = [];
                $index = -1;
                foreach ($data as $line) {
                    $parts = explode(' = ', $line);
                    if (str_starts_with($parts[0], 'mask')) {
                        $operations[++$index]['mask'] = $parts[1];
                    } else {
                        $operations[$index]['mem'][substr($parts[0], 4, -1)] = (int)$parts[1];
                    }
                }
                return $operations;
            };

            $applyBitmaskOperation = static function(array &$memory, array $maskArr, array $mems): void
            {
                foreach ($mems as $memPos => $memValueDec) {
                    $memValueBinArr = str_split(str_pad(decbin($memValueDec), 36, '0', STR_PAD_LEFT));
                    for ($i = 0; $i < 36; $i ++) {
                        $memValueBinArr[$i] = $maskArr[$i] !== 'X' ? $maskArr[$i] : $memValueBinArr[$i];
                    }
                    $memory[$memPos] = bindec(implode($memValueBinArr));
                }
            };


            $variation = static function(int $start, int $end, array $memPosBinArr, array &$allVariations) use (&$variation)
            {
                for ($i = $start; $i <= $end; $i ++) {
                    if($memPosBinArr[$i] === 'X') {
                        foreach (['0','1'] as $b) {
                            $memPosBinArr[$i] = $b;
                            $variation($i, $end, $memPosBinArr, $allVariations);
                        }
                        break;
                    }
                    if ($i === $end) {
                        $allVariations[] = $memPosBinArr;
                    }
                }
            };

            $applyAddressDecoderOperation = static function(array &$memory, array $maskArr, array $mems) use ($variation): void
            {
                foreach ($mems as $memPos => $memValueDec) {
                    $memPosBinArr = str_split(str_pad(decbin($memPos), 36, '0', STR_PAD_LEFT));
                    for ($i = 0; $i < 36; $i ++) {
                        switch($maskArr[$i]) {
                            case '1':
                                $memPosBinArr[$i] = '1';
                                break;
                            case 'X':
                                $memPosBinArr[$i] = 'X';
                                break;
                            case '0':
                                break;
                        }
                    }
                    $allVariations = [];
                    $variation(0, 35, $memPosBinArr, $allVariations);
                    foreach ($allVariations as $allVariation) {
                        $memory[implode($allVariation)] = $memValueDec;
                    }
                }
            };

            $retrieveResult = static function(array $operations, Closure $delegate): string
            {
                $memory = [];
                foreach ($operations as $operation) {
                    $delegate($memory, str_split($operation['mask']), $operation['mem']);
                }
                $result = 0;
                foreach ($memory as $slot) {
                    $result = gmp_add($result, $slot);
                }
                return gmp_strval($result);
            };

            ###

            $output->writeln('Advent of Code 2020 - Day 14: Docking Data');

            if (PHP_INT_SIZE!==8) {
                throw new RuntimeException('Sorry, 64 Bit OS required...');
            }

            $filePath = $input->getArgument('input') ?? 'input.txt';
            $operations = $getData($filePath);

            # Part 1
            $output->writeln(
                'Execute the initialization program. What is the sum of all values left in memory after it completes?'
            );
            $output->writeln(sprintf('Result: %s', $retrieveResult($operations, $applyBitmaskOperation)));

            # Part 2
            $output->writeln(
                'Execute the initialization program using an emulator for a version 2 decoder chip. What is the sum of all values left in memory after it completes?'
            );
            $output->writeln(sprintf('Result: %s', $retrieveResult($operations, $applyAddressDecoderOperation)));
        }
    )
    ->run();