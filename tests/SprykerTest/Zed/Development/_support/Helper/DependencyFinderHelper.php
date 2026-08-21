<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Helper;

use Codeception\Module;
use Codeception\Stub;
use Generated\Shared\Transfer\DependencyCollectionTransfer;
use Generated\Shared\Transfer\ModuleTransfer;
use Generated\Shared\Transfer\OrganizationTransfer;
use Spryker\Zed\Development\Business\DevelopmentBusinessFactory;
use Spryker\Zed\Development\Business\DevelopmentFacade;
use Spryker\Zed\Development\Business\DevelopmentFacadeInterface;
use Spryker\Zed\Development\Business\Module\ModuleFileFinder\ModuleFileFinderInterface;
use Spryker\Zed\Development\Dependency\Facade\DevelopmentToModuleFinderFacadeInterface;
use Spryker\Zed\Development\DevelopmentConfig;
use Spryker\Zed\Development\DevelopmentDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerTest\Shared\Testify\Helper\VirtualFilesystemHelperTrait;
use Symfony\Component\Finder\Finder;

/**
 * Fixtures live on a virtual filesystem rather than in a checked-in `test_files/` directory: their
 * cross-module test imports would otherwise be reported as real dependencies of `spryker/development`
 * by the very finders under test.
 */
class DependencyFinderHelper extends Module
{
    use VirtualFilesystemHelperTrait;

    protected const string CODECEPTION_CONFIGURATION_TEMPLATE = "suites:\n    Business:\n        modules:\n            enabled:\n";

    protected const string CODECEPTION_ENABLED_MODULE_TEMPLATE = "                - %s\n";

    /**
     * Drives the module files through the real finder composite behind the facade. A finder that is
     * not registered in `DevelopmentBusinessFactory::createDependencyFinder()` reports nothing here.
     *
     * @param array<string, string> $virtualModuleFiles Module relative pathname to file contents.
     *
     * @return array<string, \Generated\Shared\Transfer\DependencyTransfer>
     */
    public function findOutgoingDependencies(
        string $organizationName,
        string $moduleName,
        string $dependencyType,
        array $virtualModuleFiles
    ): array {
        $virtualModuleDirectory = $this->buildVirtualModuleDirectory($moduleName, $virtualModuleFiles);

        $dependencyCollectionTransfer = $this->createDevelopmentFacade($virtualModuleDirectory)
            ->showOutgoingDependenciesForModule($this->buildModuleTransfer($organizationName, $moduleName), $dependencyType);

        return $this->indexDependenciesByComposerName($dependencyCollectionTransfer);
    }

    /**
     * @param array<string> $useStatements
     */
    public function buildPhpFileWithUseStatements(array $useStatements): string
    {
        $contents = "<?php\n\n";

        foreach ($useStatements as $useStatement) {
            $contents .= sprintf("use %s;\n", $useStatement);
        }

        return $contents;
    }

    /**
     * @param array<string> $enabledModuleClassNames
     */
    public function buildCodeceptionConfigurationWithEnabledModules(array $enabledModuleClassNames): string
    {
        $contents = static::CODECEPTION_CONFIGURATION_TEMPLATE;

        foreach ($enabledModuleClassNames as $enabledModuleClassName) {
            $contents .= sprintf(static::CODECEPTION_ENABLED_MODULE_TEMPLATE, $enabledModuleClassName);
        }

        return $contents;
    }

    protected function buildModuleTransfer(string $organizationName, string $moduleName): ModuleTransfer
    {
        $organizationTransfer = (new OrganizationTransfer())->setName($organizationName);

        return (new ModuleTransfer())->setName($moduleName)->setOrganization($organizationTransfer);
    }

    /**
     * @param array<string, string> $virtualModuleFiles
     */
    protected function buildVirtualModuleDirectory(string $moduleName, array $virtualModuleFiles): string
    {
        $moduleStructure = [];

        foreach ($virtualModuleFiles as $relativePathname => $contents) {
            $moduleStructure = array_merge_recursive(
                $moduleStructure,
                $this->buildNestedFileStructure(explode('/', $relativePathname), $contents),
            );
        }

        $virtualRootDirectory = $this->getVirtualFilesystemHelper()->getVirtualDirectory([$moduleName => $moduleStructure]);

        return $virtualRootDirectory . $moduleName . DIRECTORY_SEPARATOR;
    }

    /**
     * @param array<int, string> $pathFragments
     *
     * @return array<string, mixed>
     */
    protected function buildNestedFileStructure(array $pathFragments, string $contents): array
    {
        $pathFragment = array_shift($pathFragments);

        if ($pathFragments === []) {
            return [$pathFragment => $contents];
        }

        return [$pathFragment => $this->buildNestedFileStructure($pathFragments, $contents)];
    }

    protected function createDevelopmentFacade(string $virtualModuleDirectory): DevelopmentFacadeInterface
    {
        $developmentBusinessFactory = Stub::make(DevelopmentBusinessFactory::class, [
            'createModuleFileFinder' => function () use ($virtualModuleDirectory): ModuleFileFinderInterface {
                return $this->createModuleFileFinderStub($virtualModuleDirectory);
            },
        ]);
        // A stubbed factory cannot resolve its own bundle config: the mock class name carries no module name.
        $developmentBusinessFactory->setConfig(new DevelopmentConfig());
        $developmentBusinessFactory->setContainer($this->createContainerForVirtualModuleTree());

        $developmentFacade = new DevelopmentFacade();
        $developmentFacade->setFactory($developmentBusinessFactory);

        return $developmentFacade;
    }

    protected function createModuleFileFinderStub(string $virtualModuleDirectory): ModuleFileFinderInterface
    {
        /** @var \Spryker\Zed\Development\Business\Module\ModuleFileFinder\ModuleFileFinderInterface $moduleFileFinder */
        $moduleFileFinder = Stub::makeEmpty(ModuleFileFinderInterface::class, [
            'hasFiles' => function (): bool {
                return true;
            },
            'find' => function () use ($virtualModuleDirectory): Finder {
                return (new Finder())->files()->in($virtualModuleDirectory)->ignoreDotFiles(false);
            },
        ]);

        return $moduleFileFinder;
    }

    protected function createContainerForVirtualModuleTree(): Container
    {
        $container = new Container();
        $developmentDependencyProvider = new DevelopmentDependencyProvider();
        $container = $developmentDependencyProvider->provideBusinessLayerDependencies($container);

        $container->set(DevelopmentDependencyProvider::FACADE_MODULE_FINDER, $this->createModuleFinderFacadeStub());

        return $container;
    }

    /**
     * The virtual tree holds the module under test and nothing beside it, so no extension module is
     * discoverable. Scanning the real tree for one costs about twenty seconds per call.
     */
    protected function createModuleFinderFacadeStub(): DevelopmentToModuleFinderFacadeInterface
    {
        /** @var \Spryker\Zed\Development\Dependency\Facade\DevelopmentToModuleFinderFacadeInterface $moduleFinderFacade */
        $moduleFinderFacade = Stub::makeEmpty(DevelopmentToModuleFinderFacadeInterface::class, [
            'getModules' => function (): array {
                return [];
            },
            'getProjectModules' => function (): array {
                return [];
            },
            'getPackages' => function (): array {
                return [];
            },
        ]);

        return $moduleFinderFacade;
    }

    /**
     * @return array<string, \Generated\Shared\Transfer\DependencyTransfer>
     */
    protected function indexDependenciesByComposerName(DependencyCollectionTransfer $dependencyCollectionTransfer): array
    {
        $dependencyTransfers = [];

        foreach ($dependencyCollectionTransfer->getDependencyModules() as $dependencyModuleTransfer) {
            foreach ($dependencyModuleTransfer->getDependencies() as $dependencyTransfer) {
                $dependencyTransfers[(string)$dependencyModuleTransfer->getComposerName()] = $dependencyTransfer;
            }
        }

        return $dependencyTransfers;
    }
}
