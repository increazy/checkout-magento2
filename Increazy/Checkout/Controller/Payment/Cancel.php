<?php
namespace Increazy\Checkout\Controller\Payment;

use Increazy\Checkout\Controller\Controller;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
// use Magento\Framework\Registry;

class Cancel extends Controller
{
    /**
     * @var Order
     */
    private $order;
    // /**
    //  * @var Registry
    //  */
    // private $registry;


    public function __construct(
        Context $context,
        Order $order,
        // Registry $registry,
        StoreManagerInterface $store,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->order = $order;
        // $this->registry = $registry;
        parent::__construct($context, $store, $scopeConfig);
    }

    public function validate($body)
    {
        return isset($body->order_id);
    }

    public function action($body)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->get('Magento\Framework\Registry')->register('isSecureArea', true);
        $this->order->loadByIncrementId($body->order_id);
        if (stripos($this->order->getPayment()->getMethod(), 'increazy') !== false) {
            $infos = $this->order->getPayment()->getAdditionalInformation();
            if (!isset($infos['infos']['pay_method'])) {
                // $this->registry->register('isSecureArea','true');
                $this->order->delete();
                // $this->registry->unregister('isSecureArea');
            }
        }

        $objectManager->get('Magento\Framework\Registry')->unregister('isSecureArea');

        return [
            'success' => true,
        ];
    }
}
