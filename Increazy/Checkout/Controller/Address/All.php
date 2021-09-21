<?php
namespace Increazy\Checkout\Controller\Address;

use Increazy\Checkout\Controller\Controller;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;

class All extends Controller
{
    /**
     * @var Customer
     */
    private $customer;

    public function __construct(
        Context $context,
        Customer $customer,
        StoreManagerInterface $store
    )
    {
        $this->customer = $customer;
        parent::__construct($context, $store);
    }

    public function validate($body)
    {
        return isset($body->token);
    }

    public function action($body)
    {
        $customerId = $this->hashDecode($body->token);
        $this->customer->load($customerId);

        return array_values(array_map(function ($address) {
            return $address->toArray();
        }, $this->customer->getAddresses()));
    }
}
