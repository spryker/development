<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\DependencyFinder\TwigDependencyFinder;

use Spryker\Zed\Development\Business\Dependency\DependencyContainer\DependencyContainerInterface;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\Context\DependencyFinderContextInterface;
use Spryker\Zed\Development\Business\Dependency\DependencyFinder\TwigDependencyFinder;

class ViewFunctionDependencyFinder implements TwigDependencyFinderInterface
{
    /**
     * @var string
     */
    protected const TEMPLATE_FUNCTION_PATTERN = '/view\(\'(.*?),\s\'(.*?)\'/';

    public function checkDependencyInFile(
        DependencyFinderContextInterface $context,
        DependencyContainerInterface $dependencyContainer
    ): DependencyContainerInterface {
        $twigFileContent = $context->getFileInfo()->getContents();

        $pregMatchResult = preg_match_all(static::TEMPLATE_FUNCTION_PATTERN, $twigFileContent, $matches, PREG_SET_ORDER);

        if ($pregMatchResult === false || $pregMatchResult === 0) {
            return $dependencyContainer;
        }

        return $this->addFindings($context->getModule()->getName(), $matches, $dependencyContainer);
    }

    public function getType(): string
    {
        return TwigDependencyFinder::TYPE_TWIG;
    }

    protected function addFindings(string $module, array $matches, DependencyContainerInterface $dependencyContainer): DependencyContainerInterface
    {
        foreach ($matches as $match) {
            $foundModule = $match[2];
            if ($foundModule === $module) {
                continue;
            }
            $dependencyContainer->addDependency($foundModule, $this->getType(), true);
        }

        return $dependencyContainer;
    }
}
