<?php
namespace Increazy\Checkout\Controller\Address;

use Increazy\Checkout\Controller\Controller;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;

class Remove extends Controller
{
    /**
     * @var Address
     */
    private $address;
    /**
     * @var Customer
     */
    private $customer;

    public function __construct(
        Context $context,
        Address $address,
        Customer $customer,
        StoreManagerInterface $store
    )
    {
        $this->customer = $customer;
        $this->address = $address;
        parent::__construct($context, $store);
    }

    public function validate($body)
    {
        return isset($body->address_id) && isset($body->token);
    }

    public function action($body)
    {
        $this->address->load($body->address_id)->delete();

        $all = new All($this->context, $this->customer, $this->store);
        return $all->action($body);
    }
}
