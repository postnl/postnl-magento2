<?php
declare(strict_types=1);

namespace TIG\PostNL\Controller\FillIn;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use TIG\PostNL\Model\AuthWarden;

class Callback implements HttpGetActionInterface
{
    private ResultFactory $resultFactory;

    private AuthWarden $authWarden;

    private RequestInterface $request;

    public function __construct(
        ResultFactory $resultFactory,
        AuthWarden $authWarden,
        RequestInterface $request
    ) {
        $this->request = $request;
        $this->authWarden = $authWarden;
        $this->resultFactory = $resultFactory;
    }

    public function execute(): Redirect
    {
        $code = $this->request->getParam('code', '');
        $state = $this->request->getParam('state', '');

        $this->authWarden->handleCallback($code, $state);

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('checkout', ['_secure' => true]);
    }
}
