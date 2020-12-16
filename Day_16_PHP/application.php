#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setName("Day 16: Ticket Translation")
    ->addArgument('input', InputArgument::OPTIONAL, 'Specify input file, default: input.txt')
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {
            $getRestrictions = static function (string $filePath): array {
                $parts = explode("\n\n", file_get_contents($filePath));
                $lines = explode("\n", $parts[0]);
                $restrictions = [];
                foreach ($lines as $line) {
                    $parts = explode(': ', $line);
                    $restriction = ['title' => $parts[0]];
                    $ranges = explode(' or ', $parts[1]);
                    foreach ($ranges as $range) {
                        $minMax = explode('-', $range);
                        $restriction['ranges'][] = ['min' => $minMax[0], 'max' => $minMax[1]];
                    }
                    $restrictions[] = $restriction;
                }
                return $restrictions;
            };

            $getMyTicket = static function (string $filePath): array {
                $parts = explode("\n\n", file_get_contents($filePath));
                $lines = explode("\n", $parts[1]);
                return explode(",", $lines[1]);
            };

            $getNearbyTickets = static function (string $filePath): array {
                $parts = explode("\n\n", file_get_contents($filePath));
                $lines = explode("\n", $parts[2]);
                array_shift($lines);
                $nearbyTickets = [];
                foreach ($lines as $line) {
                    $nearbyTickets[] = explode(",", $line);
                }
                return $nearbyTickets;
            };

            $isCheckFieldValid = static function($field, $restrictions): bool {
                foreach ($restrictions as $restriction) {
                    foreach ($restriction['ranges'] as $range) {
                        if($field >= $range['min'] && $field <= $range['max']) {
                            return true;
                        }
                    }
                }
                return false;
            };

            $sumInvalidFields = static function(&$nearbyTickets, $restrictions) use ($isCheckFieldValid): int {
                $sumInvalidFields = 0;
                foreach ($nearbyTickets as $index => $nearbyTicket) {
                    foreach ($nearbyTicket as $field) {
                        if (! $isCheckFieldValid($field, $restrictions)) {
                            $sumInvalidFields += $field;
                            $nearbyTickets[$index] = false;
                        }
                    }
                }
                $nearbyTickets = array_filter($nearbyTickets, static function ($item) { return $item; });
                return $sumInvalidFields;
            };

            ###

            $output->writeln('Advent of Code 2020 - Day 16: Ticket Translation');

            if (PHP_INT_SIZE !== 8) {
                throw new RuntimeException('Sorry, 64 Bit OS required...');
            }

            $filePath = $input->getArgument('input') ?? 'input.txt';

            $restrictions = $getRestrictions($filePath);
            $nearbyTickets = $getNearbyTickets($filePath);
            $myTicket = $getMyTicket($filePath);

            # Part 1
            $output->writeln('Consider the validity of the nearby tickets you scanned. What is your ticket scanning error rate?');
            $result = $sumInvalidFields($nearbyTickets, $restrictions);
            $output->writeln(sprintf('Result: %s', $result));

            # Part 2
            $output->writeln('Once you work out which field is which, look for the six fields on your ticket that start with the word departure. What do you get if you multiply those six values together?');
            $result = ':_(';
            $output->writeln(sprintf('Result: %s', $result));
        }
    )
    ->run();