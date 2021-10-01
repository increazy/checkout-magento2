<?php
namespace Increazy\Checkout\Controller\Cart;

use Increazy\Checkout\Controller\Controller;
use Increazy\Checkout\Helpers\CompleteQuote;
use Magento\Framework\App\Action\Context;
use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Model\Quote;

class GetOrCreate extends Controller
{
    /**
     * @var QuoteFactory
     */
    private $quoteFactory;
    /**
     * @var Quote
     */
    private $quote;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customer;

    public function __construct(
        Context $context,
        QuoteFactory $quoteFactory,
        Quote $quote,
        CustomerRepositoryInterface $customer,
        StoreManagerInterface $store
    )
    {
        $this->quoteFactory = $quoteFactory;
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

        if (($body->quote_id ?? '') !== '') {
         $this->quote->load($body->quote_id);
         $quote = $this->quote;
        } else {
            $quote = $this->quoteFactory->create();
        }

        if (($body->token ?? '') !== '') {
            $customerId = $this->hashDecode($body->token);
            $customer = $this->customer->getById($customerId);
            $quote->assignCustomer($customer);
        }

        $quote->setStoreId($body->store);

        $quote->collectTotals()->save();

        return CompleteQuote::get($quote);
    }
}
