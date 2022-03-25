<?php
namespace Increazy\Checkout\Controller\Auth;

use Increazy\Checkout\Controller\Controller;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Customer;
use Magento\Framework\Encryption\Encryptor;
use Magento\Store\Model\StoreManagerInterface;

class Recovery extends Controller
{
    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var Encryptor
     */
    private $encryptor;


    public function __construct(
        Context $context,
        Customer $customer,
        StoreManagerInterface $store,
        Encryptor $encryptor
    )
    {
        $this->customer = $customer;
        $this->encryptor = $encryptor;

        parent::__construct($context, $store);
    }

    public function validate($body)
    {
        return isset($body->email) && isset($body->password);
    }

    public function action($body)
    {
        $this->customer->setWebsiteId($this->store->getStore()->getWebsiteId());

        $this->customer->loadByEmail($this->hashDecode($body->email));

        if (!$this->customer->getId()) {
            $this->error('customer.exists');
        }

        $this->customer->setData('password_hash', $this->encryptor->getHash($body->password));
        $this->customer->save();

        return [ true ];
    }
}
