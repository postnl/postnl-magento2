<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@postcodeservice.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@postcodeservice.com for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Controller\Adminhtml\Matrix;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use TIG\PostNL\Model\Carrier\MatrixrateRepository;
use TIG\PostNL\Service\Validation\Factory;

class Save extends Action
{
    /** @var MatrixrateRepository  */
    protected $matrixrateRepository;

    /** @var Factory  */
    protected $_validator;

    /**
     * @param Context               $context
     * @param MatrixrateRepository  $matrixrateRepository
     * @param Factory               $validator
     */
    public function __construct(
        Context $context,
        MatrixrateRepository $matrixrateRepository,
        Factory $validator
    ) {
        parent::__construct($context);
        $this->matrixrateRepository = $matrixrateRepository;
        $this->_validator           = $validator;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $model = $this->matrixrateRepository->create();

        try {
            foreach ($data['country_id'] as $countryCode) {
                // create array for validation of the region
                $regionValidationArray = [ 'country' => $countryCode, 'region'=> $data['destiny_region_id']];
                // validate the data
                $countryId      = $this->_validator->validate('country',$countryCode);
                $region         = $this->_validator->validate('region',$regionValidationArray);
                $weight         = $this->_validator->validate('weight',$data['weight']);
                $subtotal       = $this->_validator->validate('subtotal',$data['subtotal']);
                $quantity       = $this->_validator->validate('quantity',$data['quantity']);
                $price          = $this->_validator->validate('price',$data['price']);
                $parcelType     = $this->_validator->validate('parcel-type',$data['parcel_type']);
                // add data to the database
                $model->addData([
                    'website_id'            => $data['website_id'],
                    "destiny_region_id"     => $region,
                    "destiny_zip_code"      => $data['destiny_zip_code'],
                    "weight"                => $weight,
                    "subtotal"              => $subtotal,
                    "quantity"              => $quantity,
                    "price"                 => $price,
                    "parcel_type"           => $parcelType,
                    "destiny_country_id"    => $countryId
                ]);
                $this->matrixrateRepository->save($model);
                $model->unsetData();
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        $this->messageManager->addSuccessMessage( __('Data inserted successfully!') );
        $this->_redirect('*/*/index');
    }
}
