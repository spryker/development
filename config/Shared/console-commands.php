<?php

/**
 * Console commands configuration
 * Define which commands should be available in the development executable
 */

use Spryker\Zed\Development\Communication\Console\CodeArchitectureSnifferConsole;
use Spryker\Zed\Development\Communication\Console\CodePhpMessDetectorConsole;
use Spryker\Zed\Development\Communication\Console\CodePhpstanConsole;
use Spryker\Zed\Development\Communication\Console\CodeStyleSnifferConsole;

return [
    new CodeArchitectureSnifferConsole(),
    new CodePhpMessDetectorConsole(),
    new CodePhpstanConsole(),
    new CodeStyleSnifferConsole(),
];
