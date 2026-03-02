<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Composer\Validator;

use Generated\Shared\Transfer\ComposerJsonValidationResponseTransfer;
use Generated\Shared\Transfer\ValidationMessageTransfer;

class ComposerJsonUnboundRequireConstraintValidator implements ComposerJsonValidatorInterface
{
    /**
     * @var string
     */
    protected const REQUIRE = 'require';

    public function validate(
        array $composerJsonArray,
        ComposerJsonValidationResponseTransfer $composerJsonValidationResponseTransfer
    ): ComposerJsonValidationResponseTransfer {
        if (!isset($composerJsonArray[static::REQUIRE])) {
            return $composerJsonValidationResponseTransfer;
        }

        foreach ($composerJsonArray[static::REQUIRE] as $packageName => $version) {
            if (preg_match('/^ext-/', $packageName)) {
                continue;
            }
            if ($version === '*') {
                $validationMessageTransfer = new ValidationMessageTransfer();
                $validationMessageTransfer->setMessage(sprintf('Package "%s" has an unbound version constraint (*).', $packageName));
                $composerJsonValidationResponseTransfer->addValidationMessage($validationMessageTransfer);
            }
        }

        return $composerJsonValidationResponseTransfer;
    }
}
