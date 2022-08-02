<?php

namespace TIG\PostNL\Controller\Adminhtml\Matrix;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use TIG\PostNL\Model\Carrier\MatrixrateRepository;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection;
use TIG\PostNL\Service\Validation\Factory;

class Save extends Action
{
    /**
     * @var MatrixrateRepository
     */
    protected $customFactory;

    /**
     * @var Factory
     */
    protected $_validator;

    public function __construct(
        Context $context,
        MatrixrateRepository $collectionFactory,
        Factory $validator
    ) {
        parent::__construct($context);
        $this->customFactory = $collectionFactory;
        $this->_validator = $validator;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $model = $this->customFactory->create();
        try {




            foreach ($data['country_id'] as $countryCode) {

                $regionValidationArray = [ 'country' => $countryCode, 'region'=> $data['destiny_region_id']];


                $countryId      = $this->_validator->validate('country',$countryCode);
                $region         = $this->_validator->validate('region',$regionValidationArray);
                $weight         = $this->_validator->validate('weight',$data['weight']);
                $subtotal       = $this->_validator->validate('subtotal',$data['subtotal']);
                $quantity       = $this->_validator->validate('quantity',$data['quantity']);
                $price          = $this->_validator->validate('price',$data['price']);
                $parcelType     = $this->_validator->validate('parcel-type',$data['parcel_type']);

//                var_dump($countryCode);
//                var_dump($countryId);
//                die();

                $model->addData([
                    "destiny_region_id"     => $region,
                    "destiny_zip_code"      => $data['destiny_zip_code'],
                    "weight"                => $weight,
                    "subtotal"              => $subtotal,
                    "quantity"              => $quantity,
                    "price"                 => $price,
                    "parcel_type"           => $parcelType,
                    "destiny_country_id"    => $countryId
                ]);

                $this->customFactory->save($model);
                $model->unsetData();
            }



        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }
        $this->messageManager->addSuccessMessage( __('Insert data Successfully !') );

        $this->_redirect('*/*/index');
    }
}
