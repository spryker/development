<?php

namespace SprykerTest\Zed\Development\Business\Resolver;

use Codeception\Test\Unit;
use Spryker\Zed\Development\Business\Resolver\CodeStylePathResolver;
use Spryker\Zed\Development\DevelopmentConfig;
use Spryker\Zed\Development\Business\CodeStyleSniffer\Config\CodeStyleSnifferConfigurationLoaderInterface;
use Spryker\Zed\Development\Business\Normalizer\NameNormalizerInterface;
use Spryker\Zed\Development\Business\Exception\CodeStyleSniffer\PathDoesNotExistException;
use RuntimeException;

class CodeStylePathResolverTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\Development\DevelopmentBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function shouldResolveProjectPathsWithValidModule(): void
    {
        // Arrange
        $pathResolver = $this->createPathResolver();

        // Act
        $resolvedPaths = $pathResolver->resolvePaths('Development', null, null, []);

        // Assert
        $this->assertNotEmpty($resolvedPaths);
    }

    /**
     * @return void
     */
    public function shouldThrowExceptionForInvalidNamespace(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);

        // Arrange
        $pathResolver = $this->createPathResolver();

        // Act
        $pathResolver->resolvePaths(null, 'InvalidNamespace', null, []);
    }

    /**
     * @return void
     */
    public function shouldHandleAllModulesForCoreNamespace(): void
    {
        // Arrange
        $pathResolver = $this->createPathResolver();

        // Act
        $resolvedPaths = $pathResolver->resolvePaths('all', 'Spryker', null, []);

        // Assert
        $this->assertNotEmpty($resolvedPaths);
    }

    /**
     * @return void
     */
    public function shouldThrowExceptionForSuffixWithAllModules()
    {
        // Assert
        $this->expectException(RuntimeException::class);

        // Arrange
        $pathResolver = $this->createPathResolver();

        // Act
        $pathResolver->resolvePaths('all', 'Spryker', null, ['pathSuffix' => 'someSuffix']);
    }

    protected function createPathResolver(): CodeStylePathResolver
    {
        $configMock = $this->createMock(DevelopmentConfig::class);
        $nameNormalizerMock = $this->createMock(NameNormalizerInterface::class);
        $codeStyleSnifferConfigurationLoaderMock = $this->createMock(CodeStyleSnifferConfigurationLoaderInterface::class);

        return new CodeStylePathResolver($configMock, $nameNormalizerMock, $codeStyleSnifferConfigurationLoaderMock);
    }
}
