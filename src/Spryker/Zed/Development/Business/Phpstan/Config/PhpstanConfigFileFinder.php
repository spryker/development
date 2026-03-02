<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Phpstan\Config;

use SplFileInfo;
use Spryker\Zed\Development\DevelopmentConfig;
use Symfony\Component\Finder\Finder;

class PhpstanConfigFileFinder implements PhpstanConfigFileFinderInterface
{
    /**
     * @var \Symfony\Component\Finder\Finder
     */
    protected $finder;

    /**
     * @var \Spryker\Zed\Development\DevelopmentConfig
     */
    protected $config;

    public function __construct(Finder $finder, DevelopmentConfig $config)
    {
        $this->finder = $finder;
        $this->config = $config;
    }

    public function searchIn(string $directoryPath): ?SplFileInfo
    {
        $this->clearFinder();
        $this->addDirectoryToFinder($directoryPath);

        return $this->getConfigFile();
    }

    protected function getConfigFile(): ?SplFileInfo
    {
        if (!$this->finder->hasResults()) {
            return null;
        }

        $finderAsArray = iterator_to_array($this->finder, false);

        /** @phpstan-var \SplFileInfo */
        return reset($finderAsArray);
    }

    protected function addDirectoryToFinder(string $directoryPath): void
    {
        $this->finder->in($directoryPath);
    }

    protected function clearFinder(): void
    {
        $this->finder = $this->finder::create()
            ->name($this->config->getPhpstanConfigFilename())
            ->depth('== 0');
    }
}
