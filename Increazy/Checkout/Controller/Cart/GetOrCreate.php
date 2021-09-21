<?php
namespace Increazy\Checkout\Controller\Cart;

use Increazy\Checkout\Controller\Controller;
use Increazy\Checkout\Helpers\CompleteQuote;
use Magento\Framework\App\Action\Context;
use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class GetOrCreate extends Controller
{
    /**
     * @var QuoteFactory
     */
    private $quote;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customer;

    public function __construct(
        Context $context,
        QuoteFactory $quote,
        CustomerRepositoryInterface $customer,
        StoreManagerInterface $store
    )
    {
        $this->quote = $quote;
        $this->customer = $customer;
        parent::__construct($context, $store);
    }

    public function validate($body)
    {
        return true;
    }

    public function action($body)
    {
        $quote = null;

        if (isset($body->quote_id)) {
            $quote = $this->quote->create()->load($body->quote_id);
        } else {
            $quote = $this->quote->create();
        }

        if (!empty($body->token ?? '')) {
            $customerId = $this->hashDecode($body->token);
            $customer = $this->customer->getById($customerId);
            $quote->assignCustomer($customer);
        }

        $quote->collectTotals()->save();

        return CompleteQuote::get($quote);
    }
}
