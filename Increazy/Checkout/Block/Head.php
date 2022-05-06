<?php

namespace Increazy\Checkout\Block;

class head extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ){
        parent::__construct($context, $data);
    }
}