<?php
namespace Increazy\Checkout\Controller\Payment;

use Increazy\Checkout\Controller\Controller;
use Magento\Framework\App\Action\Context;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteManagement;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;

class Prepare extends Controller
{
    /**
     * @var Quote
     */
    private $quote;
    /**
     * @var QuoteManagement
     */
    private $quoteManagement;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customer;

    public function __construct(
        Context $context,
        QuoteManagement $quoteManagement,
        CustomerRepositoryInterface $customer,
        Quote $quote,
        StoreManagerInterface $store,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->quoteManagement = $quoteManagement;
        $this->quote = $quote;
        $this->customer = $customer;
        parent::__construct($context, $store, $scopeConfig);
    }

    public function validate($body)
    {
        return isset($body->token) && isset($body->payment_method) &&
            isset($body->quote_id) && isset($body->tax)
        ;
    }

    public function action($body)
    {
        $customerId = $this->hashDecode($body->token);
        $customer = $this->customer->getById($customerId);
        $this->quote->load($body->quote_id);

        $this->quote->assignCustomer($customer);
        $this->quote->setPaymentMethod($body->payment_method);
        $this->quote->setInventoryProcessed(false);
        $this->quote->save();

        $this->quote->getPayment()->importData([
            'method' => $body->payment_method
        ]);
        $this->quote->collectTotals()->save();

        $order = $this->quoteManagement->submit($this->quote);
        $order->setState(Order::STATE_PENDING_PAYMENT);
        $order->setTaxAmount($order->getTaxAmount() + $body->tax);
        $order->save();

        return [
            'increment_id' => $order->getRealOrderId(),
        ];
    }
}
