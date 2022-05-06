<?php
namespace Increazy\Checkout\Controller\Customer;

use Increazy\Checkout\Controller\Controller;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Store\Model\StoreManagerInterface;

class Update extends Controller
{
    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var Customer
     */
    private $compare;
    /**
     * @var Encryptor
     */
    private $encryptor;


    public function __construct(
        Context $context,
        Customer $customer,
        Customer $compare,
        StoreManagerInterface $store,
        Encryptor $encryptor,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->customer = $customer;
        $this->compare = $compare;
        $this->encryptor = $encryptor;

        parent::__construct($context, $store, $scopeConfig);
    }

    public function validate($body)
    {
        return isset($body->email) && isset($body->taxvat) &&
            isset($body->token) &&  isset($body->firstname) &&
            isset($body->lastname)
        ;
    }

    public function action($body)
    {
        $this->customer->setWebsiteId($this->store->getStore()->getWebsiteId());
        $this->compare->setWebsiteId($this->store->getStore()->getWebsiteId());

        $this->compare->loadByEmail($body->email);

        if ($this->customer->getId() !== $this->compare->getId()) {
            $this->error('customer.exists');
        }

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
