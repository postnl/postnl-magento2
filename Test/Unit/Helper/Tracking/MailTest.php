<?php

namespace TIG\PostNL\Test\Unit\Helper\Tracking;

use Magento\Framework\DataObject;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use TIG\PostNL\Helper\Data as PostNLHelper;
use TIG\PostNL\Helper\Tracking\Mail;
use TIG\PostNL\Model\ShipmentRepository;
use ReflectionMethod;
use TIG\PostNL\Test\TestCase;

class MailTest extends TestCase
{
    protected $instanceClass = Mail::class;

    public function testTemplateVarsDoNotOverrideTheStoreLogo()
    {
        $vars = $this->getTemplateVars();

        $this->assertArrayNotHasKey('logo_url', $vars);
    }

    public function testTemplateVarsExposeThePostNLLogo()
    {
        $vars = $this->getTemplateVars();

        $this->assertSame('https://store.test/postnl_logo.png', $vars['postnl_logo_url']);
    }

    private function getTemplateVars()
    {
        $address = new DataObject([
            'firstname'  => 'Jane',
            'middlename' => '',
            'lastname'   => 'Doe',
            'street'     => ['Teststraat 1'],
        ]);

        $shipment = new class ($address) extends DataObject {
            private $address;

            public function __construct($address)
            {
                parent::__construct(['is_pakjegemak' => false]);
                $this->address = $address;
            }

            public function getOriginalShippingAddress()
            {
                return $this->address;
            }

            public function getDeliveryDateFormatted($format = 'd-m-Y H:i:s')
            {
                return '01-01-2026';
            }
        };

        $repository = $this->getFakeMock(ShipmentRepository::class)
            ->onlyMethods(['getByFieldWithValue'])
            ->getMock();
        $repository->method('getByFieldWithValue')->willReturn($shipment);

        $helper = $this->getFakeMock(PostNLHelper::class)->onlyMethods(['getDate'])->getMock();
        $helper->method('getDate')->willReturn('2026-01-01');

        $assets = $this->getFakeMock(AssetRepository::class)->onlyMethods(['getUrlWithParams'])->getMock();
        $assets->method('getUrlWithParams')->willReturn('https://store.test/postnl_logo.png');

        $order = new DataObject(['id' => 1, 'billing_address' => $address]);

        $instance = $this->getInstance([
            'postNLShipmentRepository' => $repository,
            'data'                     => $helper,
            'assetRepository'          => $assets,
        ]);

        $method = new ReflectionMethod($instance, 'getTemplateVars');

        return $method->invoke($instance, $order, 'https://postnl.test/track');
    }
}
