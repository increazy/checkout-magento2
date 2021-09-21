<?php
namespace Increazy\Checkout\Controller\Order;

use Increazy\Checkout\Controller\Controller;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class All extends Controller
{
    /**
     * @var CollectionFactory
     */
    private $collection;

    public function __construct(
        Context $context,
        CollectionFactory  $collection,
        StoreManagerInterface $store
    )
    {
        $this->collection = $collection;
        parent::__construct($context, $store);
    }

    public function validate($body)
    {
        return isset($body->token) && isset($body->payment_data) && isset($body->quote_id);
    }

    public function action($body)
    {
        $customerId = $this->hashDecode($body->token);
        $orders = $this->collection->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $customerId)
        ->setOrder('created_at', 'desc');

        return $orders->toArray()['items'];
    }
}
