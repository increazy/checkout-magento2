<?php
namespace Increazy\Checkout\Controller\Payment;

use Increazy\Checkout\Controller\Controller;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;

class Process extends Controller
{
    /**
     * @var Order
     */
    private $order;


    public function __construct(
        Context $context,
        Order $order,
        StoreManagerInterface $store,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->order = $order;
        parent::__construct($context, $store, $scopeConfig);
    }

    public function validate($body)
    {
        return isset($body->payment_data) &&
            isset($body->order_id)
        ;
    }

    public function action($body)
    {
        $this->order->loadByIncrementId($body->order_id);

        $paymentData = json_decode(json_encode($body->payment_data), true);
        $this->order->getPayment()->setAdditionalInformation([
            'infos' => $paymentData
        ]);
        // var_dump(json_encode(get_class_methods($this->order)));
        // var_dump(json_encode(get_class_methods($this->order->getPayment())));

        $this->order->setEmailSent(1);
        $this->order->getPayment()->save();
        $this->order->save();

        return array_merge($this->order->getData(), [
            'shipping'       => $this->order->getShippingAddress()->getData(),
            'billing'        => $this->order->getBillingAddress()->getData(),
            'delivery_name'  => $this->order->getShippingDescription(),
            'delivery_price' => $this->order->getShippingAmount(),
            'discount'       => $this->order->getDiscountAmount(),
            'total'          => $this->order->getGrandTotal(),
            'tax'            => $this->order->getTaxAmount(),
            'payment_method' => [
                'additional_info' => $this->order->getPayment()->getAdditionalInformation(),
                'additional_data' => $this->order->getPayment()->getAdditionalData(),
                'method'          => $this->order->getPayment()->getMethod(),
            ],
        ]);
    }
}
