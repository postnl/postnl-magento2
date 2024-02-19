<?php

namespace TIG\PostNL\Service\Timeframe;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Helper\Data as PostNLHelper;

class IsPastCutOff
{
    /**
     * @var Webshop
     */
    private $webshop;

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var TimezoneInterface
     */
    private $currentDate;

    /**
     * @var PostNLHelper
     */
    private $postNLHelper;

    /**
     * @param Webshop           $webshop
     * @param ShippingOptions   $shippingOptions
     * @param TimezoneInterface $currentDate
     * @param PostNLHelper      $postNLHelper
     */
    public function __construct(
        Webshop $webshop,
        ShippingOptions $shippingOptions,
        TimezoneInterface $currentDate,
        PostNLHelper $postNLHelper
    ) {
        $this->webshop = $webshop;
        $this->shippingOptions = $shippingOptions;
        $this->currentDate = $currentDate;
        $this->postNLHelper = $postNLHelper;
    }

    /**
     * @return bool
     */
    public function calculate()
    {
        $nowTime = strtotime($this->now()->format('H:i:s'));
        $cutOffTime = strtotime($this->cutOffTime());

        return $nowTime > $cutOffTime;
    }

    /**
     * @return bool
     */
    public function calculateToday()
    {
        $nowTime    = strtotime($this->now()->format('H:i:s'));
        $cutOffTime = strtotime($this->shippingOptions->getTodayCutoffTime());

        return $nowTime > $cutOffTime;
    }

    /**
     * @return \DateTime
     */
    private function now()
    {
        return $this->currentDate->date('now', null, true, false);
    }

    /**
     * @return mixed
     */
    private function cutOffTime()
    {
        $day = $this->postNLHelper->getDayOrWeekNumber($this->now()->format('H:i:s'));

        return $cutOffTime = $this->webshop->getCutOffTimeForDay($day);
    }
}
