<?php
declare(strict_types=1);

namespace TIG\PostNL\Cron;

class ExpiredLabelCleanupCronjob
{
    public const LABEL_CLEANUP_INTERVAL = '4 months';

    /**
     * @var \TIG\PostNL\Model\ResourceModel\ShipmentLabel\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \TIG\PostNL\Config\Provider\Webshop
     */
    private $webshop;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timezone;

    /**
     * @param \TIG\PostNL\Model\ResourceModel\ShipmentLabel\CollectionFactory $collectionFactory
     * @param \TIG\PostNL\Config\Provider\Webshop $webshop
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \TIG\PostNL\Model\ResourceModel\ShipmentLabel\CollectionFactory $collectionFactory,
        \TIG\PostNL\Config\Provider\Webshop $webshop,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->webshop = $webshop;
        $this->timezone = $timezone;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        if ($this->webshop->isExpiredLabelCleanupEnabled()) {
            $this->collectionFactory->create()
                ->cleanupExpiredLabels(
                    $this->timezone->date()->modify('-' . self::LABEL_CLEANUP_INTERVAL)
                );
        }
    }
}
