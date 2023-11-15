<?php

declare(strict_types=1);

namespace HyvaCheckout\PurchaseOrderNumber\Model\ConfigData\HyvaThemes;

use Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigComponents;

class SystemConfigPurchaseOrderNumber
{
    protected SystemConfigComponents $systemConfigComponents;

    public function __construct(
        SystemConfigComponents $systemConfigComponents
    ) {
        $this->systemConfigComponents = $systemConfigComponents;
    }

    public function getPlaceholderText(): string
    {
        return $this->systemConfigComponents->getComponentValue('placeholder', 'purchase_order_number') ?? '';
    }
}
