<?php

declare(strict_types=1);

namespace HyvaCheckout\PurchaseOrderNumber\Magewire\Checkout;

use Exception;
use Hyva\Checkout\Model\Magewire\Component\EvaluationInterface;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultFactory;
use Hyva\Checkout\Model\Magewire\Component\EvaluationResultInterface;
use HyvaCheckout\PurchaseOrderNumber\Model\ConfigData\HyvaThemes\SystemConfigPurchaseOrderNumber;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magewirephp\Magewire\Component;

class PurchaseOrderNumber extends Component implements EvaluationInterface
{
    public ?string $purchase_order_number = null;
    public bool $saved = false;

    protected SessionCheckout $sessionCheckout;
    protected CartRepositoryInterface $quoteRepository;
    protected SystemConfigPurchaseOrderNumber $systemConfig;

    public function __construct(
        SessionCheckout $sessionCheckout,
        CartRepositoryInterface $quoteRepository,
        SystemConfigPurchaseOrderNumber $systemConfig
    ) {
        $this->sessionCheckout = $sessionCheckout;
        $this->quoteRepository = $quoteRepository;
        $this->systemConfig = $systemConfig;
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function mount(): void
    {
        $quote = $this->sessionCheckout->getQuote();
        $purchase_order_number = $quote->getExtensionAttributes()->getPurchaseOrderNumber();

        $this->purchase_order_number = $purchase_order_number;
    }

    public function updatingPurchaseOrderNumber(string $value): string
    {
        try {
            $quote = $this->sessionCheckout->getQuote();
            $quote->getExtensionAttributes()->setPurchaseOrderNumber($value);

            $this->quoteRepository->save($quote);
            $this->saved = true;
        } catch (LocalizedException | Exception $exception) {
            $this->dispatchErrorMessage('Something went wrong while saving your purchase order number. Please try again.');
        }

        return $value;
    }

    public function evaluateCompletion(EvaluationResultFactory $resultFactory): EvaluationResultInterface
    {
        if ($this->systemConfig->isRequired() && empty($this->purchase_order_number)) {
            return $resultFactory->createErrorEvent(
                ['error' => __('This is a required field.')],
                'po-number-evaluation-error-event'
            );
        }

        return $resultFactory->createSuccess(
            ['error' => ''],
            'po-number-evaluation-error-event'
        );
    }
}
