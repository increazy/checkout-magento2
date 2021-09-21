<?php
namespace Increazy\Checkout\Controller\Customer;

use Increazy\Checkout\Controller\Controller;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Customer;
use Magento\Framework\Encryption\Encryptor;
use Magento\Store\Model\StoreManagerInterface;

class Register extends Controller
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
        return isset($body->email) && isset($body->password) &&
            isset($body->firstname) && isset($body->lastname) &&
            isset($body->taxvat)
        ;
    }

    public function action($body)
    {
        $this->customer->setWebsiteId($this->store->getStore()->getWebsiteId());
        foreach ($body as $key => $value) {
            if ($key === 'password') {
                $key = 'password_hash';
                $value = $this->encryptor->getHash($value);
            }

            $this->customer->setData($key, $value);
        }

        $this->customer->save();

        return [
            'customer' => $this->customer->getData(),
            'token'    => $this->hashEncode($this->customer->getId()),
        ];
    }
}
