<?php
namespace Increazy\Checkout\Controller\Coupon;

use Increazy\Checkout\Controller\Controller;
use Increazy\Checkout\Helpers\CompleteQuote;
use Magento\Framework\App\Action\Context;
use Magento\Quote\Model\Quote;
use Magento\SalesRule\Model\Coupon;
use Magento\Store\Model\StoreManagerInterface;

class Add extends Controller
{
    /**
     * @var Quote
     */
    private $quote;
    /**
     * @var Coupon
     */
    private $coupon;

    public function __construct(
        Context $context,
        Quote $quote,
        Coupon $coupon,
        StoreManagerInterface $store
    )
    {
        $this->quote = $quote;
        $this->coupon = $coupon;
        parent::__construct($context, $store);
    }

    public function validate($body)
    {
        return isset($body->coupon) && isset($body->quote_id);
    }

    public function action($body)
    {
        $this->quote->load($body->quote_id);
        $this->quote->setCouponCode($body->coupon);

        $this->quote->collectTotals()->save();

        return CompleteQuote::get($this->quote);
    }
}
