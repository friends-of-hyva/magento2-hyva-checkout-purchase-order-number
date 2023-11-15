<?php

declare(strict_types=1);

namespace HyvaCheckout\PurchaseOrderNumber\Plugin\Magento\Sales\Api;

use Exception;
use Magento\Sales\Api\OrderRepositoryInterface as Subject;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Psr\Log\LoggerInterface;

class OrderRepository
{
    protected OrderExtensionFactory $orderExtensionFactory;
    protected LoggerInterface $logger;

    /**
     * @param OrderExtensionFactory $orderExtensionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        LoggerInterface $logger
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->logger = $logger;
    }

    /**
     * @param Subject $subject
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterGet(Subject $subject, OrderInterface $order): OrderInterface
    {
        $this->setPurchaseOrderNumber($order);
        return $order;
    }

    /**
     * @param Subject $subject
     * @param OrderSearchResultInterface $orderSearchResult
     * @return OrderSearchResultInterface
     */
    public function afterGetList(Subject $subject, OrderSearchResultInterface $orderSearchResult): OrderSearchResultInterface
    {
        foreach ($orderSearchResult->getItems() as $order) {
            $this->setPurchaseOrderNumber($order);
        }

        return $orderSearchResult;
    }

    /**
     * @param OrderInterface $order
     */
    public function setPurchaseOrderNumber(OrderInterface $order): void
    {
        try {
            $extensionAttributes = $order->getExtensionAttributes();

            /** @var OrderExtensionInterface $target */
            $target = $extensionAttributes ?? $this->orderExtensionFactory->create();
            $target->setPurchaseOrderNumber($order->getPurchaseOrderNumber());

            $order->setExtensionAttributes($target);
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
        }
    }

    /**
     * @param Subject $subject
     * @param OrderInterface $order
     * @return array
     */
    public function beforeSave(Subject $subject, OrderInterface $order): array
    {
        try {
            $purchaseOrderNumber = $order->getExtensionAttributes()->getPurchaseOrderNumber();

            if (is_string($purchaseOrderNumber)) {
                $order->setPurchaseOrderNumber($purchaseOrderNumber);
            }
        } catch (Exception $exception) {
            $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
        }

        return [$order];
    }
}
