<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Development\Helper;

use Codeception\Module;

class SnifferConfigurationHelper extends Module
{
    /**
     * @var string
     */
    protected const PATH_SPRYKER_ZED_ACL_MODULE = 'ConfigurationReader/Spryker/Zed/Acl/';

    /**
     * @var string
     */
    protected const PATH_SPRYKER_ZED_CUSTOMER_MODULE = 'ConfigurationReader/Spryker/Zed/Customer/';

    /**
     * @var string
     */
    protected const PATH_SPRYKER_ZED_COUNTRY_MODULE = 'ConfigurationReader/Spryker/Zed/Country/';

    /**
     * @var string
     */
    protected const PATH_SPRYKER_ZED_DISCOUNT_MODULE = 'ConfigurationReader/Spryker/Zed/Discount/';

    /**
     * @var string
     */
    protected const PATH_SPRYKER_ZED_PRODUCT_MODULE = 'ConfigurationReader/Spryker/Zed/Product/';

    /**
     * @var string
     */
    protected const PATH_CUSTOM_FOLDER = 'ConfigurationReader/custom/';

    public function getZedAclModulePath(): string
    {
        return $this->getModuleAbsolutePath(static::PATH_SPRYKER_ZED_ACL_MODULE);
    }

    public function getZedCustomerModulePath(): string
    {
        return $this->getModuleAbsolutePath(static::PATH_SPRYKER_ZED_CUSTOMER_MODULE);
    }

    public function getZedDiscountModulePath(): string
    {
        return $this->getModuleAbsolutePath(static::PATH_SPRYKER_ZED_DISCOUNT_MODULE);
    }

    public function getZedProductModulePath(): string
    {
        return $this->getModuleAbsolutePath(static::PATH_SPRYKER_ZED_PRODUCT_MODULE);
    }

    public function getZedCustomPath(): string
    {
        return $this->getModuleAbsolutePath(static::PATH_CUSTOM_FOLDER);
    }

    public function getZedCountryPath(): string
    {
        return $this->getModuleAbsolutePath(static::PATH_SPRYKER_ZED_COUNTRY_MODULE);
    }

    protected function getModuleAbsolutePath(string $path): string
    {
        return codecept_data_dir() . $path;
    }
}
