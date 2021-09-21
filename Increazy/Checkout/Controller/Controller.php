<?php
namespace Increazy\Checkout\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Store\Model\StoreManagerInterface;

abstract class Controller extends Action
{
    const HASH = '484625ce159b74746a7f966e671ac2c4';

    /**
     * @var StoreManagerInterface
     */
    protected $store;
    /**
     * @var Context
     */
    protected $context;

    public function __construct(Context $context, StoreManagerInterface $store) {
        $this->context = $context;
        $this->store = $store;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $body = file_get_contents("php://input");
            $body = json_decode($body);
            if (!isset($body->store)) {
                $this->error('store.not-found');
            }

            // if (!isset(getallheaders()['token'])) {
            //     $this->error('access.not-found');
            // }

            // if ($this->hashDecode(getallheaders()['token']) !== 'increazy') {
            //     $this->error('access.not-found');
            // }

            $this->store->setCurrentStore($body->store);

            header('Content-Type:application/json');

            if (!$this->validate($body)) {
                $this->error('body.not-found');
            }

            $response = $this->action($body);
            die(json_encode($response));
        } catch (\Exception $e) {
            die(json_encode([
                'message' => $e->getMessage(),
            ]));
        }
    }

    public function error($msg)
    {
        http_response_code(400);
        throw new \Exception($msg);
    }

    public function hashDecode($str)
    {
        $token = base64_decode(self::HASH);
		$parts = explode(':', $token);
		$key = hash('sha256', $parts[0]);
		$iv = substr(hash('sha256', $parts[1]), 0, 16);

		return openssl_decrypt(base64_decode($str), "AES-256-CBC", $key, 0, $iv);
    }

    public function hashEncode($str)
    {
        if ($str == '') return '';
        $token = base64_decode(self::HASH);
        $parts = explode(':', $token);
        $key = hash('sha256', $parts[0]);
        $iv = substr(hash('sha256', $parts[1]), 0, 16);

        $output = openssl_encrypt($str, "AES-256-CBC", $key, 0, $iv);
        return base64_encode($output);
    }

    public abstract function action($body);
    public abstract function validate($body);
}
