<?php
namespace Increazy\Checkout\Controller\Customer;

use Increazy\Checkout\Controller\Controller;
use Magento\Framework\App\Action\Context;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;

class Newsletter extends Controller
{
    /**
     * @var Subscriber
     */
    private $subscriber;


    public function __construct(
        Context $context,
        Subscriber $subscriber,
        StoreManagerInterface $store
    )
    {
        $this->subscriber = $subscriber;

        parent::__construct($context, $store);
    }

    public function validate($body)
    {
        return isset($body->email);
    }

    public function action($body)
    {
        $this->subscriber->subscribe($body->email);

        return [
            'email' => $body->email,
        ];
    }
}
