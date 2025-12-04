<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Business\Composer\Updater;

use Codeception\Test\Unit;
use Spryker\Zed\Development\Business\Composer\Updater\AutoloadUpdater;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Development
 * @group Business
 * @group Composer
 * @group Updater
 * @group AutoloadUpdaterTest
 * Add your own group annotations below this line
 */
class AutoloadUpdaterTest extends Unit
{
    /**
     * @return void
     */
    public function testWhenTestsFolderExistsDefaultAutoloadDevIsAddedToComposer(): void
    {
        $updatedJson = $this->updateJsonForTests($this->getComposerJson());

        $this->assertArrayHasKey('autoload-dev', $updatedJson);
        $this->assertSame($this->getComposerJson()['autoload-dev'], $updatedJson['autoload-dev']);
    }

    /**
     * @dataProvider autoloadKeys
     *
     * @param string $autoloadKey
     *
     * @return void
     */
    public function testWhenDeprecatedDirExistsAutoloadDevAddedToComposer(string $autoloadKey): void
    {
        $updatedJson = $this->getJsonAfterUpdate(
            [
                AutoloadUpdater::BASE_TESTS_DIRECTORY,
                $autoloadKey,
            ],
            $this->getComposerJson($autoloadKey),
        );
        $this->assertSame($this->getComposerJson($autoloadKey)['autoload-dev'], $updatedJson['autoload-dev']);
    }

    /**
     * @return void
     */
    public function testWhenTestFolderDoesNotExistNothingAddedToComposer(): void
    {
        $splFileInfo = $this->getSplFile();
        $composerJson = $this->getComposerJson();
        $autoloadUpdaterMock = $this->getAutoloadUpdaterMock();
        $autoloadUpdaterMock->method('pathExists')->willReturn(false);

        $updatedComposerJson = $autoloadUpdaterMock->update($composerJson, $splFileInfo);
        $this->assertArrayNotHasKey('autoload', $updatedComposerJson, 'autoload empty and thus removed.');
        $this->assertArrayNotHasKey('autoload-dev', $updatedComposerJson, 'autoload-dev empty and thus removed.');
    }

    /**
     * @return void
     */
    public function testWhenAutoloadDevNamespaceIsInvalidGetsRemoved(): void
    {
        $composerJson = $this->getComposerJson();
        $composerJson['autoload-dev']['psr-4']['invalidNamespace'] = 'validDirectory/';

        $updatedJson = $this->updateJsonForTests($composerJson);

        $this->assertArrayHasKey('autoload-dev', $updatedJson);
        $this->assertSame($this->getComposerJson()['autoload-dev'], $updatedJson['autoload-dev']);
    }

    /**
     * @return void
     */
    public function testWhenAutoloadPathIsInvalidGetsRemoved(): void
    {
        $composerJson = $this->getComposerJson();
        $composerJson['autoload']['psr-4']['validNamespace'] = 'invalidDirectory/';

        $updatedJson = $this->updateJsonForTests($composerJson);

        $this->assertArrayHasKey('autoload-dev', $updatedJson);
        $this->assertSame($this->getComposerJson()['autoload-dev'], $updatedJson['autoload-dev']);
    }

    /**
     * @return void
     */
    public function testWhenSupportFolderExistsWithHelpersItGetsAddedToAutoload(): void
    {
        $pathParts = [
            AutoloadUpdater::BASE_SRC_DIRECTORY,
            AutoloadUpdater::SPRYKER_NAMESPACE,
        ];

        $updatedJson = $this->getJsonAfterUpdate(
            [
                AutoloadUpdater::BASE_SRC_DIRECTORY . '/' . AutoloadUpdater::SPRYKER_NAMESPACE,
            ],
            $this->getComposerJson(),
            [
                [
                    $pathParts,
                    implode(DIRECTORY_SEPARATOR, $pathParts) . '/',
                ],
            ],
        );

        $this->assertSame($this->getComposerJson()['autoload'], $updatedJson['autoload']);
    }

    /**
     * @param array $composerJson
     *
     * @return array
     */
    protected function updateJsonForTests(array $composerJson): array
    {
        $pathParts = [
            AutoloadUpdater::BASE_TESTS_DIRECTORY,
            AutoloadUpdater::SPRYKER_TEST_NAMESPACE,
        ];

        return $this->getJsonAfterUpdate(
            [
                AutoloadUpdater::BASE_TESTS_DIRECTORY,
                AutoloadUpdater::SPRYKER_TEST_NAMESPACE,
            ],
            $composerJson,
            [
                [
                    $pathParts,
                    implode(DIRECTORY_SEPARATOR, $pathParts) . '/',
                ],
            ],
        );
    }

    /**
     * @param array $pathParts
     * @param array $composerJson
     * @param array $dirMapAdditions
     *
     * @return array
     */
    protected function getJsonAfterUpdate(array $pathParts, array $composerJson, array $dirMapAdditions = []): array
    {
        $splFileInfo = $this->getSplFile();
        $modulePath = dirname($splFileInfo->getPathname());

        $autoloadUpdaterMock = $this->getAutoloadUpdaterMock();

        $autoloadUpdaterMock->method('getPath')->willReturnCallback(
            function (array $parts) {
                return implode(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR;
            },
        );

        $validPaths = [];

        $expectedPath = implode(DIRECTORY_SEPARATOR, array_merge([$modulePath], $pathParts)) . DIRECTORY_SEPARATOR;
        $validPaths[] = $expectedPath;

        foreach ($dirMapAdditions as $addition) {
            if (is_array($addition) && isset($addition[0])) {
                $additionPathParts = $addition[0];
                $validPaths[] = $modulePath . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $additionPathParts) . DIRECTORY_SEPARATOR;
            }
        }

        if (isset($composerJson['autoload-dev']['psr-4'])) {
            foreach ($composerJson['autoload-dev']['psr-4'] as $namespace => $relativeDirectory) {
                if (strpos($namespace, 'Test') !== false && strpos($relativeDirectory, 'tests/') === 0) {
                    $normalizedRelDir = str_replace('/', DIRECTORY_SEPARATOR, rtrim($relativeDirectory, '/'));
                    $validPaths[] = $modulePath . DIRECTORY_SEPARATOR . $normalizedRelDir . DIRECTORY_SEPARATOR;
                }
            }
        }

        if (isset($composerJson['autoload']['psr-4'])) {
            foreach ($composerJson['autoload']['psr-4'] as $namespace => $relativeDirectory) {
                if (strpos($namespace, 'Spryker') !== false && strpos($relativeDirectory, 'src/') === 0) {
                    $normalizedRelDir = str_replace('/', DIRECTORY_SEPARATOR, rtrim($relativeDirectory, '/'));
                    $validPaths[] = $modulePath . DIRECTORY_SEPARATOR . $normalizedRelDir . DIRECTORY_SEPARATOR;
                }
            }
        }

        $autoloadUpdaterMock->method('pathExists')->willReturnCallback(
            function ($path) use ($validPaths, $modulePath) {
                $pathNormalized = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

                foreach ($validPaths as $validPath) {
                    $validPathNormalized = rtrim($validPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

                    if ($pathNormalized === $validPathNormalized) {
                        return true;
                    }
                }

                return false;
            },
        );

        $autoloadUpdaterMock->method('getNonEmptyDirectoriesWithHelpers')->willReturn([]);

        return $autoloadUpdaterMock->update($composerJson, $splFileInfo);
    }

    /**
     * @return array
     */
    public function autoloadKeys(): array
    {
        return [
            ['Acceptance'],
            ['Functional'],
            ['Integration'],
            ['Unit'],
        ];
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Development\Business\Composer\Updater\AutoloadUpdater
     */
    protected function getAutoloadUpdaterMock(): AutoloadUpdater
    {
        $autoloadUpdaterMock = $this->getMockBuilder(AutoloadUpdater::class)
            ->onlyMethods(['pathExists', 'getPath', 'getNonEmptyDirectoriesWithHelpers'])
            ->getMock();

        return $autoloadUpdaterMock;
    }

    /**
     * @param string $autoloadKey
     *
     * @return array
     */
    protected function getComposerJson(string $autoloadKey = ''): array
    {
        $composerArray = [
            'autoload' => [
                'psr-4' => [
                    'Spryker' => 'src/Spryker',
                ],
            ],
            'autoload-dev' => [
                'psr-4' => [
                    'SprykerTest\\' => 'tests/SprykerTest/',
                ],
            ],
        ];

        if ($autoloadKey) {
            $composerArray['autoload-dev']['psr-0'] = [$autoloadKey => 'tests/'];
            unset($composerArray['autoload-dev']['psr-4']);
        }

        return $composerArray;
    }

    /**
     * @return \Symfony\Component\Finder\SplFileInfo
     */
    protected function getSplFile(): SplFileInfo
    {
        return new SplFileInfo(__FILE__, __DIR__, __DIR__);
    }
}
