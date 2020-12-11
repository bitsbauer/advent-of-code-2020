#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setName("Day 10")
    ->addArgument('input', InputArgument::OPTIONAL, 'Specify input file, default: input.txt')
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {
            $getData = static function (string $filePath): array {
                $data = file_get_contents($filePath);
                return explode("\n", $data);
            };

            $addPlugAndDevice = static function (array $adapters): array {
                sort($adapters);
                $highestAdapter = $adapters[array_key_last($adapters)];
                $adapters[] = gmp_strval(gmp_add($highestAdapter, 3));
                array_unshift($adapters, '0');
                return $adapters;
            };

            $findAllDifferences = static function (array $adapters) use ($addPlugAndDevice): array {
                $adapters = $addPlugAndDevice($adapters);
                $differences = ['1' => 0, '2' => 0, '3' => 0];
                $count = count($adapters);
                for ($i = 0; $i < $count - 1; $i++) {
                    $assocIndex = gmp_strval(gmp_sub($adapters[$i + 1], $adapters[$i]));
                    $differences[$assocIndex]++;
                }
                return $differences;
            };

            $findPaths = static function ($lastPaths, $finalPaths, $adapters, $highestAdapter) use ( &$findPaths ): array {
                $currentPaths = [];
                foreach ($lastPaths as $lastPath => $count) {
                    if($lastPath === $highestAdapter) {
                        $finalPaths[$highestAdapter] += $count;
                        continue;
                    }
                    for ($i = 1; $i < 4; $i++) {
                        $value = $lastPath + $i;
                        if (in_array($value, $adapters, true)) {
                            $currentPaths[$value] = isset($currentPaths[$value])
                                ? $currentPaths[$value] + $count
                                : $count;
                        }
                    }
                }
                return count($currentPaths) > 0
                    ? $findPaths($currentPaths, $finalPaths, $adapters, $highestAdapter)
                    : $finalPaths;
            };

            $findAllPaths = static function (array $adapters) use ($findPaths): int {
                $adapters = array_map(static function ($adapter) { return (int)$adapter; }, $adapters);
                sort($adapters);
                $highestAdapter = $adapters[array_key_last($adapters)];
                $result = $findPaths([0 => 1], [$highestAdapter => 0], $adapters, $highestAdapter);
                return $result[$highestAdapter];
            };

            ###

            $output->writeln('Advent of Code 2020 - Day 10');

            $filePath = $input->getArgument('input') ?? 'input.txt';

            $adapters = $getData($filePath);

            # Part 1
            $output->writeln(
                'What is the number of 1-jolt differences multiplied by the number of 3-jolt differences?'
            );
            $differences = $findAllDifferences($adapters);
            $result = $differences['1'] * $differences['3'];
            $output->writeln(sprintf('Result: %s', $result));

            # Part 2
            $output->writeln(
                'What is the total number of distinct ways you can arrange the adapters to connect the charging outlet to your device?'
            );
            $result = $findAllPaths($adapters);
            $output->writeln(sprintf('Result: %s', $result));
        }
    )
    ->run();
