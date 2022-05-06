<?php
namespace Increazy\Checkout\Controller\Cart;

use Increazy\Checkout\Controller\Controller;
use Increazy\Checkout\Helpers\CompleteQuote;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;

class Remove extends Controller
{
    /**
     * @var Quote
     */
    private $quote;
    /**
     * @var Product
     */
    private $product;

    public function __construct(
        Context $context,
        Quote $quote,
        Product $product,
        StoreManagerInterface $store,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->quote = $quote;
        $this->product = $product;
        parent::__construct($context, $store, $scopeConfig);
    }

    public function validate($body)
    {
        return isset($body->product_id) && isset($body->quote_id);
    }

    public function action($body)
    {
        $this->quote->load($body->quote_id);
        $this->quote->setStore($this->store->getStore());
        $this->product->load($body->product_id);

        $item = $this->quote->getItemByProduct($this->product);
        $this->quote->removeItem($item->getId());

        $this->quote->collectTotals()->save();

        return CompleteQuote::get($this->quote);
    }
}
