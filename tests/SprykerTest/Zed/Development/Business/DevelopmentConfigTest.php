<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Business;

use PHPUnit\Framework\TestCase;
use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Zed\Development\DevelopmentConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Development
 * @group Business
 * @group DevelopmentConfigTest
 * Add your own group annotations below this line
 */
class DevelopmentConfigTest extends TestCase
{
    /**
     * @var string
     */
    protected const NAMESPACE_PYZ = 'Pyz';

    /**
     * @var string
     */
    protected const NAMESPACE_SPRYKER_ACADEMY = 'SprykerAcademy';

    /**
     * @var string
     */
    protected const NAMESPACE_CUSTOM_PROJECT = 'CustomProject';

    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        if (!defined('APPLICATION_ROOT_DIR')) {
            define('APPLICATION_ROOT_DIR', sys_get_temp_dir());
        }
        if (!defined('APPLICATION_VENDOR_DIR')) {
            define('APPLICATION_VENDOR_DIR', sys_get_temp_dir() . '/vendor');
        }
        if (!defined('APPLICATION_SOURCE_DIR')) {
            define('APPLICATION_SOURCE_DIR', sys_get_temp_dir() . '/src');
        }
    }

    /**
     * @return void
     */
    public function testGetIdeAutoCompletionSourceDirectoryGlobPatternsReturnsVendorPattern(): void
    {
        // Arrange
        $developmentConfigMock = $this->createDevelopmentConfigMock([static::NAMESPACE_PYZ]);

        // Act
        $patterns = $developmentConfigMock->getIdeAutoCompletionSourceDirectoryGlobPatterns();

        // Assert
        $this->assertArrayHasKey(APPLICATION_VENDOR_DIR . '/*/*/src/', $patterns);
        $this->assertSame('*/*/', $patterns[APPLICATION_VENDOR_DIR . '/*/*/src/']);
    }

    /**
     * @return void
     */
    public function testGetIdeAutoCompletionSourceDirectoryGlobPatternsReturnsSingleProjectNamespace(): void
    {
        // Arrange
        $developmentConfigMock = $this->createDevelopmentConfigMock([static::NAMESPACE_PYZ]);

        // Act
        $patterns = $developmentConfigMock->getIdeAutoCompletionSourceDirectoryGlobPatterns();

        // Assert
        $expectedKey = APPLICATION_SOURCE_DIR . '/' . static::NAMESPACE_PYZ . '/';
        $this->assertArrayHasKey($expectedKey, $patterns);
        $this->assertSame('*/', $patterns[$expectedKey]);
    }

    /**
     * @return void
     */
    public function testGetIdeAutoCompletionSourceDirectoryGlobPatternsReturnsAllProjectNamespaces(): void
    {
        // Arrange
        $projectNamespaces = [
            static::NAMESPACE_PYZ,
            static::NAMESPACE_SPRYKER_ACADEMY,
            static::NAMESPACE_CUSTOM_PROJECT,
        ];
        $developmentConfigMock = $this->createDevelopmentConfigMock($projectNamespaces);

        // Act
        $patterns = $developmentConfigMock->getIdeAutoCompletionSourceDirectoryGlobPatterns();

        // Assert
        foreach ($projectNamespaces as $namespace) {
            $expectedKey = APPLICATION_SOURCE_DIR . '/' . $namespace . '/';
            $this->assertArrayHasKey(
                $expectedKey,
                $patterns,
                sprintf('Expected pattern key for namespace "%s" not found.', $namespace),
            );
            $this->assertSame('*/', $patterns[$expectedKey]);
        }

        // Verify total count: 1 vendor pattern + N project namespace patterns
        $this->assertCount(count($projectNamespaces) + 1, $patterns);
    }

    /**
     * @return void
     */
    public function testGetIdeAutoCompletionSourceDirectoryGlobPatternsReturnsOnlyVendorPatternWhenNoProjectNamespaces(): void
    {
        // Arrange
        $developmentConfigMock = $this->createDevelopmentConfigMock([]);

        // Act
        $patterns = $developmentConfigMock->getIdeAutoCompletionSourceDirectoryGlobPatterns();

        // Assert
        $this->assertCount(1, $patterns);
        $this->assertArrayHasKey(APPLICATION_VENDOR_DIR . '/*/*/src/', $patterns);
    }

    /**
     * @param array<string> $projectNamespaces
     *
     * @return \Spryker\Zed\Development\DevelopmentConfig|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createDevelopmentConfigMock(array $projectNamespaces): DevelopmentConfig
    {
        $developmentConfigMock = $this->getMockBuilder(DevelopmentConfig::class)
            ->onlyMethods(['get'])
            ->getMock();

        $developmentConfigMock
            ->method('get')
            ->willReturnCallback(function (string $key, $default = null) use ($projectNamespaces) {
                if ($key === KernelConstants::PROJECT_NAMESPACES) {
                    return $projectNamespaces;
                }

                return $default;
            });

        return $developmentConfigMock;
    }
}
