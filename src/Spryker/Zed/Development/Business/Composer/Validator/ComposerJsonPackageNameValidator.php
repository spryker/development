<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Composer\Validator;

use Generated\Shared\Transfer\ComposerJsonValidationResponseTransfer;
use Generated\Shared\Transfer\ValidationMessageTransfer;

class ComposerJsonPackageNameValidator implements ComposerJsonValidatorInterface
{
    /**
     * @var string
     */
    protected const REQUIRE = 'require';

    /**
     * @var string
     */
    protected const REQUIRE_DEV = 'require-dev';

    public function validate(
        array $composerJsonArray,
        ComposerJsonValidationResponseTransfer $composerJsonValidationResponseTransfer
    ): ComposerJsonValidationResponseTransfer {
        $require = $composerJsonArray[static::REQUIRE] ?? [];
        foreach ($require as $packageName => $version) {
            $composerJsonValidationResponseTransfer = $this->assertPackageName($packageName, $composerJsonValidationResponseTransfer);
        }

        $requireDev = $composerJsonArray[static::REQUIRE_DEV] ?? [];
        foreach ($requireDev as $packageName => $version) {
            $composerJsonValidationResponseTransfer = $this->assertPackageName($packageName, $composerJsonValidationResponseTransfer);
        }

        return $composerJsonValidationResponseTransfer;
    }

    protected function assertPackageName(
        string $packageName,
        ComposerJsonValidationResponseTransfer $composerJsonValidationResponseTransfer
    ): ComposerJsonValidationResponseTransfer {
        if (mb_strtolower($packageName) !== $packageName) {
            $validationMessageTransfer = new ValidationMessageTransfer();
            $validationMessageTransfer->setMessage(sprintf('Package name `%s` is not in valid lowercase only format.', $packageName));
            $composerJsonValidationResponseTransfer->addValidationMessage($validationMessageTransfer);
        }

        return $composerJsonValidationResponseTransfer;
    }
}
