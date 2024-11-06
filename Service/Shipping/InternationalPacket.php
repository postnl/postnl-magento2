<?php
namespace TIG\PostNL\Service\Shipping;

use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Model\Order\Item as OrderItem;
use TIG\PostNL\Service\Shipment\EpsCountries;

class InternationalPacket extends BoxablePackets
{
    // Works the same way as boxable, but using another attribute.
    protected const ATTRIBUTE_KEY = 'postnl_max_qty_international';
}
