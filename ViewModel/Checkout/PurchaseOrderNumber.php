<?php

declare(strict_types=1);

namespace HyvaCheckout\PurchaseOrderNumber\ViewModel\Checkout;

use HyvaCheckout\PurchaseOrderNumber\Model\ConfigData\HyvaThemes\SystemConfigPurchaseOrderNumber;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PurchaseOrderNumber implements ArgumentInterface
{
    private SystemConfigPurchaseOrderNumber $systemConfigPurchaseOrderNumber;

    public function __construct(
        SystemConfigPurchaseOrderNumber $systemConfigPurchaseOrderNumber
    ) {
        $this->systemConfigPurchaseOrderNumber = $systemConfigPurchaseOrderNumber;
    }

    public function hasPlaceholderText(): bool
    {
        return !empty($this->getPlaceholderText());
    }

    public function getPlaceholderText(): string
    {
        return $this->systemConfigPurchaseOrderNumber->getPlaceholderText();
    }
}
