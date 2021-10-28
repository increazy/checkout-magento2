<?php

namespace Increazy\Checkout\Block\Info;

class Info extends \Magento\Payment\Block\Info
{
    public function getSpecificInformation()
    {
        $info = $this->getInfo()->getAdditionalInformation();
        return $info['infos']['response'];
    }

    public function toPdf()
    {
        $this->setTemplate('Increazy_Checkout::info/pdf/info.phtml');
        return $this->toHtml();
    }

    public function toHtml()
    {
        $info = (array)$this->getInfo()->getAdditionalInformation();
        $data = $info['infos'];

        if (!isset($data['pay_method']) || !isset($data['status']) || !isset($data['response'])) {
            return '';
        }

        $method = $this->getMethodLabel($data['pay_method']);
        $status = $this->getCheckouttatus($data['status']);

        $lines = '';
        if (isset($data['response']['url'])) {
            $url = $data['response']['url'];

            $lines .= $this->createLine('URL', "<a href='$url'>Acessar</a>");

            if (strtolower($method) === 'pix') {
                $lines .= $this->createLine('QrCode', "<img src='$url' style='width:200px;height:200px'/>");
            }


            if (isset($data['response']['expiration'])) {
                $lines .= $this->createLine('Vencimento', $data['response']['expiration']);
            }
        }

        return $this->generateHTML([
            'method'  => $method,
            'status'  => $status,
            'lines'   => $lines,
            'gateway' => $data['id'],
        ]);
    }

    private function generateHTML($infos)
    {
        $html = file_get_contents(__DIR__.'/./table.html');

        foreach ($infos as $name => $value) {
            $html = str_replace("{{$name}}", $value, $html);
        }

        return $html;
    }

    private function createLine($label, $value)
    {
        return "<tr><th>$label</th><td>$value</td></tr>";
    }

    private function getMethodlabel($method)
    {
        $method = str_replace('increazy-', '', $method);

        if ($method == 'creditcard')
            return "Cartão de crédito";
        else if ($method == 'onetap')
            return "Cartão de crédito (1 clique)";
        else if ($method == 'debitcard')
            return "Cartão de débito";
        else if ($method == 'free')
            return "Grátis";
        else if ($method == 'billet')
            return 'Boleto';
        else if ($method == 'pix')
            return 'Pix';

        return 'Outro';
    }

    private function getCheckouttatus($status)
    {
        if ($status == 'canceled')
            return "Cancelado";
        else if ($status == 'pending')
            return "Pendente";
        else if ($status == 'success')
            return "Pago";

        return "Aguardando pagamento";
    }
}