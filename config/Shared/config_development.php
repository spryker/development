<?php

use Spryker\Shared\Development\DevelopmentConstants;
use Spryker\Zed\Development\Communication\Console\CodeArchitectureSnifferConsole;
use Spryker\Zed\Development\Communication\Console\CodePhpMessDetectorConsole;
use Spryker\Zed\Development\Communication\Console\CodePhpstanConsole;
use Spryker\Zed\Development\Communication\Console\CodeStyleSnifferConsole;

$config[DevelopmentConstants::STANDALONE_COMMANDS] = [
    new CodeArchitectureSnifferConsole(),
    new CodePhpMessDetectorConsole(),
    new CodePhpstanConsole(),
    new CodeStyleSnifferConsole(),
];
