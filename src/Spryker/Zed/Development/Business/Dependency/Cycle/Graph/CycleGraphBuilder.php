<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency\Cycle\Graph;

use Generated\Shared\Transfer\ComposerDependencyTransfer;
use Generated\Shared\Transfer\CycleDetectionRequestTransfer;
use Generated\Shared\Transfer\ModuleTransfer;
use Laminas\Filter\FilterChain;
use Laminas\Filter\StringToLower;
use Laminas\Filter\Word\CamelCaseToDash;
use Spryker\Zed\Development\Business\Dependency\ModuleDependencyParserInterface;
use Spryker\Zed\Development\Business\DependencyTree\ComposerDependencyParserInterface;
use Spryker\Zed\Development\Dependency\Facade\DevelopmentToModuleFinderFacadeInterface;
use Spryker\Zed\Development\DevelopmentConfig;

class CycleGraphBuilder implements CycleGraphBuilderInterface
{
    /**
     * Note: spryker-feature meta-packages appear as declared nodes only — they contain no PHP code,
     * so they never surface in the usage graph. This asymmetry is expected.
     *
     * @var string
     */
    protected const EXTENSION_SUFFIX = 'extension';

    protected DevelopmentToModuleFinderFacadeInterface $moduleFinderFacade;

    protected ModuleDependencyParserInterface $moduleDependencyParser;

    protected ComposerDependencyParserInterface $composerDependencyParser;

    protected DevelopmentConfig $developmentConfig;

    public function __construct(
        DevelopmentToModuleFinderFacadeInterface $moduleFinderFacade,
        ModuleDependencyParserInterface $moduleDependencyParser,
        ComposerDependencyParserInterface $composerDependencyParser,
        DevelopmentConfig $developmentConfig
    ) {
        $this->moduleFinderFacade = $moduleFinderFacade;
        $this->moduleDependencyParser = $moduleDependencyParser;
        $this->composerDependencyParser = $composerDependencyParser;
        $this->developmentConfig = $developmentConfig;
    }

    /**
     * @param \Generated\Shared\Transfer\CycleDetectionRequestTransfer $cycleDetectionRequestTransfer
     *
     * @return array<string, array<string, bool>>
     */
    public function buildDeclaredGraph(CycleDetectionRequestTransfer $cycleDetectionRequestTransfer): array
    {
        $allowedOrganizations = $this->getAllowedOrganizations();
        $graph = [];

        foreach ($this->getCoreModules() as $moduleTransfer) {
            $from = $this->buildComposerName($moduleTransfer);
            if (!$this->isAllowedComposerName($from, $allowedOrganizations)) {
                continue;
            }

            $graph[$from] = $graph[$from] ?? [];

            $composerDependencyCollectionTransfer = $this->composerDependencyParser
                ->getDeclaredComposerDependencies($moduleTransfer);

            foreach ($composerDependencyCollectionTransfer->getComposerDependencies() as $composerDependencyTransfer) {
                $to = $composerDependencyTransfer->getName();
                if (!$this->shouldAddEdge($from, $to, $composerDependencyTransfer, $cycleDetectionRequestTransfer, $allowedOrganizations)) {
                    continue;
                }

                $graph[$from][$to] = true;
            }
        }

        return $graph;
    }

    /**
     * @param \Generated\Shared\Transfer\CycleDetectionRequestTransfer $cycleDetectionRequestTransfer
     *
     * @return array<string, array<string, bool>>
     */
    public function buildUsageGraph(CycleDetectionRequestTransfer $cycleDetectionRequestTransfer): array
    {
        $allowedOrganizations = $this->getAllowedOrganizations();
        $graph = [];

        foreach ($this->getCoreModules() as $moduleTransfer) {
            $from = $this->buildComposerName($moduleTransfer);
            if (!$this->isAllowedComposerName($from, $allowedOrganizations)) {
                continue;
            }

            $graph[$from] = $graph[$from] ?? [];

            $dependencyCollectionTransfer = $this->moduleDependencyParser->parseOutgoingDependencies($moduleTransfer);

            foreach ($dependencyCollectionTransfer->getDependencyModules() as $dependencyModuleTransfer) {
                $to = $dependencyModuleTransfer->getComposerName();
                if ($to === null || $to === '') {
                    continue;
                }

                if (!$this->isAllowedComposerName($to, $allowedOrganizations)) {
                    continue;
                }

                if ($from === $to) {
                    continue;
                }

                if ($this->isExtensionPair($from, $to) && !$cycleDetectionRequestTransfer->getIncludeExtensions()) {
                    continue;
                }

                $graph[$from][$to] = true;
            }
        }

        return $graph;
    }

    /**
     * @return array<\Generated\Shared\Transfer\ModuleTransfer>
     */
    protected function getCoreModules(): array
    {
        $moduleTransferCollection = $this->moduleFinderFacade->getModules();
        $coreModules = [];

        foreach ($moduleTransferCollection as $moduleTransfer) {
            if (!$moduleTransfer instanceof ModuleTransfer) {
                continue;
            }
            $organizationTransfer = $moduleTransfer->getOrganization();
            if ($organizationTransfer === null || $organizationTransfer->getIsProject()) {
                continue;
            }
            $coreModules[] = $moduleTransfer;
        }

        return $coreModules;
    }

    protected function buildComposerName(ModuleTransfer $moduleTransfer): string
    {
        $organizationNameDashed = $moduleTransfer->getOrganization()?->getNameDashed();
        if (!$organizationNameDashed) {
            $organizationNameDashed = $this->dasherize((string)$moduleTransfer->getOrganization()?->getName());
        }

        $moduleNameDashed = $moduleTransfer->getNameDashed();
        if (!$moduleNameDashed) {
            $moduleNameDashed = $this->dasherize((string)$moduleTransfer->getName());
        }

        return sprintf('%s/%s', $organizationNameDashed, $moduleNameDashed);
    }

    /**
     * @return array<string>
     */
    protected function getAllowedOrganizations(): array
    {
        $allowed = [];
        foreach ($this->developmentConfig->getCoreNamespaces() as $namespace) {
            $allowed[] = $this->dasherize($namespace);
        }

        return $allowed;
    }

    /**
     * @param string $composerName
     * @param array<string> $allowedOrganizations
     *
     * @return bool
     */
    protected function isAllowedComposerName(string $composerName, array $allowedOrganizations): bool
    {
        if (strpos($composerName, '/') === false) {
            return false;
        }

        [$organization] = explode('/', $composerName, 2);

        return in_array($organization, $allowedOrganizations, true);
    }

    /**
     * @param string $from
     * @param string|null $to
     * @param \Generated\Shared\Transfer\ComposerDependencyTransfer $composerDependencyTransfer
     * @param \Generated\Shared\Transfer\CycleDetectionRequestTransfer $cycleDetectionRequestTransfer
     * @param array<string> $allowedOrganizations
     *
     * @return bool
     */
    protected function shouldAddEdge(
        string $from,
        ?string $to,
        ComposerDependencyTransfer $composerDependencyTransfer,
        CycleDetectionRequestTransfer $cycleDetectionRequestTransfer,
        array $allowedOrganizations
    ): bool {
        if ($to === null || $to === '') {
            return false;
        }

        if ($from === $to) {
            return false;
        }

        if (!$this->isAllowedComposerName($to, $allowedOrganizations)) {
            return false;
        }

        if ($composerDependencyTransfer->getIsOptional()) {
            return false;
        }

        if ($composerDependencyTransfer->getIsDev() && !$cycleDetectionRequestTransfer->getIncludeRequireDev()) {
            return false;
        }

        if ($this->isExtensionPair($from, $to) && !$cycleDetectionRequestTransfer->getIncludeExtensions()) {
            return false;
        }

        return true;
    }

    protected function isExtensionPair(string $composerNameA, string $composerNameB): bool
    {
        return $this->isExtensionOf($composerNameA, $composerNameB)
            || $this->isExtensionOf($composerNameB, $composerNameA);
    }

    protected function isExtensionOf(string $candidate, string $baseComposerName): bool
    {
        $suffix = sprintf('-%s', static::EXTENSION_SUFFIX);

        return str_starts_with($candidate, $baseComposerName) && str_ends_with($candidate, $suffix);
    }

    protected function dasherize(string $value): string
    {
        $filterChain = new FilterChain();
        $filterChain
            ->attach(new CamelCaseToDash())
            ->attach(new StringToLower());

        return $filterChain->filter($value);
    }
}
