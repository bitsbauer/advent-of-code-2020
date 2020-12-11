#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setName("Day 11")
    ->addArgument('input', InputArgument::OPTIONAL, 'Specify input file, default: input.txt')
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {
            $getData = static function (string $filePath): array {
                $data = file_get_contents($filePath);
                $data = explode("\n", $data);
                return array_map(
                    static function ($row) {
                        return str_split($row);
                    },
                    $data
                );
            };

            $countOccupied = static function (array $seatRows): int {
                $count = 0;
                foreach ($seatRows as $rowIndex => $seatRow) {
                    foreach ($seatRow as $seatIndex => $seat) {
                        if ($seat === '#') {
                            $count++;
                        }
                    }
                }
                return $count;
            };

            $applyRules = static function (array $seatRows) use (&$applyRules): array {
                $newSeatRows = [];
                $countAdjacentOccupiedSeats = static function ($rowIndex, $seatIndex) use ($seatRows): int {
                    $count = 0;
                    for ($r = $rowIndex - 1; $r < $rowIndex + 2; $r++) {
                        for ($s = $seatIndex - 1; $s < $seatIndex + 2; $s++) {
                            if ($s === $seatIndex && $r === $rowIndex) {
                                continue;
                            }
                            if (isset($seatRows[$r][$s]) && $seatRows[$r][$s] === '#') {
                                $count++;
                            }
                        }
                    }
                    return $count;
                };
                $getsOccupied = static function ($rowIndex, $seatIndex) use ($countAdjacentOccupiedSeats): bool {
                    return $countAdjacentOccupiedSeats($rowIndex, $seatIndex) === 0;
                };
                $getsEmpty = static function ($rowIndex, $seatIndex) use ($countAdjacentOccupiedSeats): bool {
                    return $countAdjacentOccupiedSeats($rowIndex, $seatIndex) >= 4;
                };
                foreach ($seatRows as $rowIndex => $seatRow) {
                    foreach ($seatRow as $seatIndex => $seat) {
                        if ($seat === 'L' && $getsOccupied($rowIndex, $seatIndex)) {
                            $newSeatRows[$rowIndex][$seatIndex] = '#';
                        } elseif ($seat === '#' && $getsEmpty($rowIndex, $seatIndex)) {
                            $newSeatRows[$rowIndex][$seatIndex] = 'L';
                        } else {
                            $newSeatRows[$rowIndex][$seatIndex] = $seat;
                        }
                    }
                }
                return $seatRows === $newSeatRows
                    ? $newSeatRows
                    : $applyRules($newSeatRows);
            };

            $applyNewRules = static function (array $seatRows) use (&$applyNewRules): array {
                $newSeatRows = [];
                $countTheOccupiedSeats = static function ($rowIndex, $seatIndex) use ($seatRows): int {
                    $count = 0;
                    $rowLength = count($seatRows[0]);
                    $rowsCount = count($seatRows);

                    $doBreakCheckSeat = static function ($checkSeat) use (&$count): bool {
                        if ($checkSeat === '.') {
                            return false;
                        }
                        if ($checkSeat === '#') {
                            $count++;
                        }
                        return true;
                    };

                    $i = 1;
                    while ($seatIndex - $i >= 0) {
                        if (!isset($seatRows[$rowIndex][$seatIndex - $i]) || $doBreakCheckSeat(
                                $seatRows[$rowIndex][$seatIndex - $i]
                            )) {
                            break;
                        }
                        $i++;
                    }

                    $i = 1;
                    while ($seatIndex + $i <= $rowLength) {
                        if (!isset($seatRows[$rowIndex][$seatIndex + $i]) || $doBreakCheckSeat(
                                $seatRows[$rowIndex][$seatIndex + $i]
                            )) {
                            break;
                        }
                        $i++;
                    }

                    $i = 1;
                    $checkSeats = [0 => false, 1 => false, 2 => false];
                    for ($r = $rowIndex - 1; $r >= 0; $r--) {
                        foreach ($checkSeats as $pos => $checkSeat) {
                            if (!$checkSeat) {
                                if ($pos === 0) {
                                    if (!isset($seatRows[$r][$seatIndex - $i]) || $doBreakCheckSeat(
                                            $seatRows[$r][$seatIndex - $i]
                                        )) {
                                        $checkSeats[$pos] = true;
                                    }
                                }
                                if ($pos === 1) {
                                    if (!isset($seatRows[$r][$seatIndex + $i]) || $doBreakCheckSeat(
                                            $seatRows[$r][$seatIndex + $i]
                                        )) {
                                        $checkSeats[$pos] = true;
                                    }
                                }
                                if ($pos === 2) {
                                    if (!isset($seatRows[$r][$seatIndex]) || $doBreakCheckSeat(
                                            $seatRows[$r][$seatIndex]
                                        )) {
                                        $checkSeats[$pos] = true;
                                    }
                                }
                            }
                        }
                        if ($checkSeats[0] && $checkSeats[1] && $checkSeats[2]) {
                            break;
                        }
                        $i++;
                    }

                    $i = 1;
                    $checkSeats = [0 => false, 1 => false, 2 => false];
                    for ($r = $rowIndex + 1; $r <= $rowsCount; $r++) {
                        foreach ($checkSeats as $pos => $checkSeat) {
                            if (!$checkSeat) {
                                if ($pos === 0) {
                                    if (!isset($seatRows[$r][$seatIndex - $i]) || $doBreakCheckSeat(
                                            $seatRows[$r][$seatIndex - $i]
                                        )) {
                                        $checkSeats[$pos] = true;
                                    }
                                }
                                if ($pos === 1) {
                                    if (!isset($seatRows[$r][$seatIndex + $i]) || $doBreakCheckSeat(
                                            $seatRows[$r][$seatIndex + $i]
                                        )) {
                                        $checkSeats[$pos] = true;
                                    }
                                }
                                if ($pos === 2) {
                                    if (!isset($seatRows[$r][$seatIndex]) || $doBreakCheckSeat(
                                            $seatRows[$r][$seatIndex]
                                        )) {
                                        $checkSeats[$pos] = true;
                                    }
                                }
                            }
                        }
                        if ($checkSeats[0] && $checkSeats[1] && $checkSeats[2]) {
                            break;
                        }
                        $i++;
                    }
                    return $count;
                };
                $getsOccupied = static function ($rowIndex, $seatIndex) use ($countTheOccupiedSeats): bool {
                    return $countTheOccupiedSeats($rowIndex, $seatIndex) === 0;
                };
                $getsEmpty = static function ($rowIndex, $seatIndex) use ($countTheOccupiedSeats): bool {
                    return $countTheOccupiedSeats($rowIndex, $seatIndex) >= 5;
                };
                foreach ($seatRows as $rowIndex => $seatRow) {
                    foreach ($seatRow as $seatIndex => $seat) {
                        if ($seat === 'L' && $getsOccupied($rowIndex, $seatIndex)) {
                            $newSeatRows[$rowIndex][$seatIndex] = '#';
                        } elseif ($seat === '#' && $getsEmpty($rowIndex, $seatIndex)) {
                            $newSeatRows[$rowIndex][$seatIndex] = 'L';
                        } else {
                            $newSeatRows[$rowIndex][$seatIndex] = $seat;
                        }
                    }
                }
                return $seatRows === $newSeatRows
                    ? $newSeatRows
                    : $applyNewRules($newSeatRows);
            };
            ###

            $output->writeln('Advent of Code 2020 - Day 11: Seating System');

            $filePath = $input->getArgument('input') ?? 'input.txt';

            $seatRows = $getData($filePath);

            # Part 1
            $output->writeln(
                'Simulate your seating area by applying the seating rules repeatedly until no seats change state. How many seats end up occupied?'
            );
            $result = $countOccupied($applyRules($seatRows));
            $output->writeln(sprintf('Result: %s', $result));

            # Part 2
            $output->writeln(
                'Given the new visibility method and the rule change for occupied seats becoming empty, once equilibrium is reached, how many seats end up occupied?'
            );
            $result = $countOccupied($applyNewRules($seatRows));
            $output->writeln(sprintf('Result: %s', $result));
        }
    )
    ->run();