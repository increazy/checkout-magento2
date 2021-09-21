<?php
namespace Increazy\Checkout\Model;

class AbstractMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = '';
    protected $_infoBlockType = \Increazy\Checkout\Block\Info\Info::class;
    protected $_isOffline = true;

    public function assignData(\Magento\Framework\DataObject $data)
    {
        $this->getInfoInstance()->setAdditionalInformation('infos', $data->getData('additional_data'));

        return $this;
    }
}
