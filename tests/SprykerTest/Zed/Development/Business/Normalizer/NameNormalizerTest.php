<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Business\Normalizer;

use Codeception\Test\Unit;
use Spryker\Zed\Development\Business\Normalizer\NameNormalizer;

class NameNormalizerTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\Development\DevelopmentBusinessTester
     */
    protected $tester;

    /**
     * @return array<array<string>>
     */
    public function dasherizeDataProvider()
    {
        return [
            ['TestName', 'test-name'],
            ['testName', 'test-name'],
            ['TestNameWithMultipleCAPS', 'test-name-with-multiple-caps'],
            ['', ''],
        ];
    }

    /**
     * @return array<array<string>>
     */
    public function camelizeDataProvider()
    {
        return [
            ['test-name', 'TestName'],
            ['test_name', 'TestName'],
            ['test-name_with_mixed', 'TestNameWithMixed'],
            ['', ''],
        ];
    }

    /**
     * @dataProvider dasherizeDataProvider
     *
     * @return void
     */
    public function shouldDasherizeCamelCaseToDash($input, $expected): void
    {
        // Arrange
        $nameNormalizer = new NameNormalizer();

        // Act
        $result = $nameNormalizer->dasherize($input);

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * @dataProvider camelizeDataProvider
     *
     * @return void
     */
    public function shouldCamelizeUnderscoreToCamelCase($input, $expected): void
    {
        // Arrange
        $nameNormalizer = new NameNormalizer();

        // Act
        $result = $nameNormalizer->camelize($input);

        // Assert
        $this->assertSame($expected, $result);
    }
}
