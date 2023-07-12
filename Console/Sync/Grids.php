<?php

namespace TIG\PostNL\Console\Sync;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TIG\PostNL\Model\ShipmentRepository;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Symfony\Component\Console\Helper\ProgressBar;
use Magento\Framework\Console\Cli;
use Magento\Setup\Module\Di\App\Task\OperationException;

// @codingStandardsIgnoreFile
class Grids extends Command
{
    const POSTNLCLI_COMMAND = 'postnl:sync:grids';
    const POSTNLCLI_COMMENT = 'Synchronizes the order- and shipment grid columns';

    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * ConfirmStatus constructor.
     *
     * @param ShipmentRepository    $shipmentRepository
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param null                  $name
     */
    public function __construct
    (
        ShipmentRepository $shipmentRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        $name = null
    ) {
        $this->shipmentRepository    = $shipmentRepository;
        $this->searchCriteriaBuilder = $criteriaBuilder;

        parent::__construct($name);
    }

    /**
     * Configuration for bin/magento action
     */
    // @codingStandardsIgnoreLine
    protected function configure()
    {
        $this->setName(static::POSTNLCLI_COMMAND);
        $this->setDescription(static::POSTNLCLI_COMMENT);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    // @codingStandardsIgnoreLine
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln('<info>Synchronization started.</info>');
            $this->startSync($output);
        } catch (OperationException $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }

        $output->writeln('<info>Synchronization completed</info>');

        return Cli::RETURN_SUCCESS;
    }

    /**
     * Syncronize all confirmation data within the following tabels.
     *
     * tig_postnl_shipment
     * tig_postnl_order
     * sales_order_grid
     * sales_shipment_grid
     *
     * Because we update the postnl shipment, all Observer classes for grid refresing will be triggerd automaticly.
     *
     * @param OutputInterface $output
     */
    private function startSync(OutputInterface $output)
    {
        $shipments = $this->getShipmentsToSync();
        if (!$shipments) {
            $output->writeln('<comment>Nothing to synchronize</comment>');
            return;
        }

        /** @var ProgressBar $progressBar */
        $progressBar = new ProgressBar($output, count($shipments));

        $progressBar->setFormat('verbose');
        $progressBar->start();
        $progressBar->display();

        $total = 1;
        /** @var PostNLShipment $shipment */
        foreach ($shipments as $shipment) {
            $this->updateShipment($shipment);
            $progressBar->display();
            $progressBar->advance();
            $total++;
        }

        $progressBar->finish();
        $output->writeln('');
    }

    /**
     * @param PostNLShipment $shipment
     */
    private function updateShipment(PostNLShipment $shipment)
    {
        $shipment->setConfirmed(true);
        $this->shipmentRepository->save($shipment);
    }

    /**
     * Retrieve all shipments which are confirmed.
     * @return mixed
     */
    private function getShipmentsToSync()
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('confirmed', 0)
            ->addFilter('confirmed_at', true, 'notnull');
        $shipments = $this->shipmentRepository->getList($searchCriteria->create());

        return $shipments->getItems();
    }
}
