<?php
namespace Increazy\Checkout\Controller\Cart;

use Increazy\Checkout\Controller\Controller;
use Increazy\Checkout\Helpers\CompleteQuote;
use Magento\Framework\App\Action\Context;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;

class Change extends Controller
{
    /**
     * @var Quote
     */
    private $quote;

    public function __construct(
        Context $context,
        Quote $quote,
        StoreManagerInterface $store
    )
    {
        $this->quote = $quote;
        parent::__construct($context, $store);
    }

    public function validate($body)
    {
        return isset($body->item_id) && isset($body->quote_id) && isset($body->quantity);
    }

    public function action($body)
    {
        $this->quote->load($body->quote_id);
        $this->quote->setStore($this->store->getStore());

        $item = $this->quote->getItemById($body->item_id);
        $item->setQty((double)$body->quantity)->save();

        $this->quote->collectTotals()->save();

        return CompleteQuote::get($this->quote);
    }
}
