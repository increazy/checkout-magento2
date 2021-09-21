<?php
namespace Increazy\Checkout\Controller\Customer;

use Increazy\Checkout\Controller\Controller;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Customer;
use Magento\Store\Model\StoreManagerInterface;

class Exists extends Controller
{
    /**
     * @var Customer
     */
    private $customer;


    public function __construct(Context $context, Customer $customer, StoreManagerInterface $store)
    {
        $this->customer = $customer;
        parent::__construct($context, $store);
    }

    public function validate($body)
    {
        return isset($body->email);
    }

    public function action($body)
    {
        $this->customer->setWebsiteId($this->store->getStore()->getWebsiteId());
        $this->customer->loadByEmail($body->email);

        if (!$this->customer->getId()) {
            $this->error('customer.exists');
        }

        return [
            'email' => $this->customer->getEmail(),
        ];
    }
}
