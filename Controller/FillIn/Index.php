<?php
declare(strict_types=1);

namespace TIG\PostNL\Controller\FillIn;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use TIG\PostNL\Model\AuthWarden;

class Index implements HttpPostActionInterface
{
    private ResultFactory $resultFactory;

    private AuthWarden $authWarden;

    public function __construct(
        ResultFactory $resultFactory,
        AuthWarden $authWarden
    ) {
        $this->authWarden = $authWarden;
        $this->resultFactory = $resultFactory;
    }

    public function execute(): Json
    {
        $data = ['success' => true, 'redirect_url' => $this->authWarden->generateRedirect()];

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($data);
    }
}
