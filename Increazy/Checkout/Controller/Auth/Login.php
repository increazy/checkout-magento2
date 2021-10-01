<?php
namespace Increazy\Checkout\Controller\Auth;

use Increazy\Checkout\Controller\Controller;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Customer;
use Magento\Store\Model\StoreManagerInterface;

class Login extends Controller
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
        return isset($body->email) && isset($body->password);
    }

    public function action($body)
    {
        $this->customer->setWebsiteId($this->store->getStore()->getWebsiteId());
        $this->customer->loadByEmail($body->email);
        $logged = $this->customer->authenticate($body->email, $body->password);

        if (!$logged) {
            $this->error('customer.credentials');
        }

        return [
            'customer' => $this->customer->getData(),
            'token'    => $this->hashEncode($this->customer->getId()),
            'id'       => $this->customer->getId(),
            'test'     => $this->hashEncode('17'),
        ];
    }
}
