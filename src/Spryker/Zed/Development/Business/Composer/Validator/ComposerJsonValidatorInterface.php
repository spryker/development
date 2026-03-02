<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Composer\Validator;

use Generated\Shared\Transfer\ComposerJsonValidationResponseTransfer;

interface ComposerJsonValidatorInterface
{
    public function validate(
        array $composerJsonArray,
        ComposerJsonValidationResponseTransfer $composerJsonValidationResponseTransfer
    ): ComposerJsonValidationResponseTransfer;
}
