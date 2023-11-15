<?php

declare(strict_types=1);

namespace HyvaCheckout\PurchaseOrderNumber\Plugin\Magento\Quote\Api;

use Exception;
use Magento\Quote\Api\CartRepositoryInterface as Subject;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Api\Data\CartExtensionInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartSearchResultsInterface;
use Psr\Log\LoggerInterface;

class CartRepository
{
    protected CartExtensionFactory $cartExtensionFactory;
    protected LoggerInterface $logger;

    /**
     * @param CartExtensionFactory $cartExtensionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CartExtensionFactory $cartExtensionFactory,
        LoggerInterface $logger
    ) {
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->logger = $logger;
    }

    /**
     * @param Subject $subject
     * @param CartInterface $quote
     * @return CartInterface
     */
    public function afterGet(Subject $subject, CartInterface $quote): CartInterface
    {
        $this->setPurchaseOrderNumber($quote);
        return $quote;
    }

    /**
     * @param Subject $subject
     * @param CartSearchResultsInterface $quoteSearchResult
     * @return CartSearchResultsInterface
     */
    public function afterGetList(Subject $subject, CartSearchResultsInterface $quoteSearchResult): CartSearchResultsInterface
    {
        foreach ($quoteSearchResult->getItems() as $quote) {
            $this->setPurchaseOrderNumber($quote);
        }

        return $quoteSearchResult;
    }

    /**
     * @param CartInterface $quote
     */
    public function setPurchaseOrderNumber(CartInterface $quote): void
    {
        try {
            $extensionAttributes = $quote->getExtensionAttributes();

            /** @var CartExtensionInterface $target */
            $target = $extensionAttributes ?? $this->cartExtensionFactory->create();
            $target->setPurchaseOrderNumber($quote->getPurchaseOrderNumber());

            $quote->setExtensionAttributes($target);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
        }
    }

    /**
     * @param Subject $subject
     * @param CartInterface $quote
     * @return array
     */
    public function beforeSave(Subject $subject, CartInterface $quote): array
    {
        try {
            $purchaseOrderNumber = $quote->getExtensionAttributes()->getPurchaseOrderNumber();

            if (is_string($purchaseOrderNumber)) {
                $quote->setPurchaseOrderNumber($purchaseOrderNumber);
            }
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
        }

        return [$quote];
    }
}
