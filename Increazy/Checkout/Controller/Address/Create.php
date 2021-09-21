<?php
namespace Increazy\Checkout\Controller\Address;

use Increazy\Checkout\Controller\Controller;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Directory\Model\ResourceModel\Region\Collection;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;

class Create extends Controller
{
    /**
     * @var Address
     */
    private $address;
    /**
     * @var Customer
     */
    private $customer;
    /**
     * @var Collection
     */
    private $collection;


    public function __construct(
        Context $context,
        Address $address,
        Customer $customer,
        Collection $collection,
        StoreManagerInterface $store
    )
    {
        $this->collection = $collection;
        $this->address = $address;
        $this->customer = $customer;
        parent::__construct($context, $store);
    }

    public function validate($body)
    {
        return isset($body->state) && isset($body->firstname) &&
            isset($body->lastname) && isset($body->postcode) &&
            isset($body->street) && isset($body->city) &&
            isset($body->telephone) && isset($body->token)
        ;
    }

    public function action($body)
    {
        $customerId = $this->hashDecode($body->token);
        $this->collection->loadWithFilter()->addRegionNameFilter($body->state)->load();

        $region = $this->collection->getData()[0];
        $body->region_id = $region['region_id'];

        $this->address->setCustomerId($customerId);

        foreach ($body as $key => $value) {
            $this->address->setData($key, $value);
        }

        $this->address->save();

        $all = new All($this->context, $this->customer, $this->store);
        return $all->action($body);
    }
}
