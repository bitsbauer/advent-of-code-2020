#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setName("Day 04")
    ->addArgument('input', InputArgument::OPTIONAL, 'Specify input file, default: input.txt')
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {
            $getData = static function (string $filePath): array {
                $data = file_get_contents($filePath);
                return explode("\n\n", trim($data));
            };
            $passportToAssoc = static function (string $passport): array {
                $passport = str_replace("\n", ' ', $passport);
                $fields = explode(' ', trim($passport));
                $assocArray = [];
                foreach ($fields as $field) {
                    [$key, $value] = explode(':', trim($field));
                    $assocArray[trim($key)] = trim($value);
                }
                return $assocArray;
            };
            $isValidPassport = static function (array $assocPassport): bool {
                $passportKeys = array_keys($assocPassport);
                $requiredKeys = ['byr', 'iyr', 'eyr', 'hgt', 'hcl', 'ecl', 'pid'];
                foreach ($requiredKeys as $requiredKey) {
                    if (false === in_array($requiredKey, $passportKeys, true)) {
                        return false;
                    }
                }
                return true;
            };
            $isValidStrictPassport = static function (array $assocPassport): bool {
                $passportKeys = array_keys($assocPassport);
                $requiredKeys = [
                    'byr' => static function ($value) {
                        return 1920 <= (int)$value && (int)$value <= 2002;
                    },
                    'iyr' => static function ($value) {
                        return 2010 <= (int)$value && (int)$value <= 2020;
                    },
                    'eyr' => static function ($value) {
                        return 2020 <= (int)$value && (int)$value <= 2030;
                    },
                    'hgt' => static function ($value) {
                        return 1 === preg_match('/^1[5-8][0-9]cm|19[0-3]cm|59in|6[0-9]in|7[0-6]in$/', $value);
                    },
                    'hcl' => static function ($value) {
                        return 1 === preg_match('/^#[0-9a-f]{6}$/', $value);
                    },
                    'ecl' => static function ($value) {
                        return in_array($value, ['amb', 'blu', 'brn', 'gry', 'grn', 'hzl', 'oth'], true);
                    },
                    'pid' => static function ($value) {
                        return 1 === preg_match('/^[0-9]{9}$/', $value);
                    },
                ];
                foreach ($requiredKeys as $requiredKey => $isValid) {
                    if (false === in_array($requiredKey, $passportKeys, true)) {
                        return false;
                    }
                    if (false === $isValid($assocPassport[$requiredKey])) {
                        return false;
                    }
                }
                return true;
            };
            $countValidPassports = static function (array $passports, bool $useStrictRules) use (
                $passportToAssoc,
                $isValidPassport,
                $isValidStrictPassport
            ): int {
                $countValid = 0;
                $delegate = $useStrictRules ? $isValidStrictPassport : $isValidPassport;
                foreach ($passports as $passport) {
                    $assocPassport = $passportToAssoc($passport);
                    if ($delegate($assocPassport)) {
                        $countValid++;
                    }
                }
                return $countValid;
            };

            ###

            $output->writeln('Advent of Code 2020 - Day 04');

            $filePath = $input->getArgument('input') ?? 'input.txt';

            $passports = $getData($filePath);

            # Part 1
            $output->writeln(
                'Count the number of valid passports - those that have all required fields. Treat cid as optional. In your batch file, how many passports are valid?'
            );
            $result = $countValidPassports($passports, false);
            $output->writeln(sprintf('Result: %s', $result));

            # Part 2
            $output->writeln(
                'Count the number of valid passports - those that have all required fields and valid values. Continue to treat cid as optional. In your batch file, how many passports are valid?'
            );
            $result = $countValidPassports($passports, true);
            $output->writeln(sprintf('Result: %s', $result));
        }
    )
    ->run();