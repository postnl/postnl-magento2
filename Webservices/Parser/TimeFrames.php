<?php

namespace TIG\PostNL\Webservices\Parser;

use TIG\PostNL\Service\Timeframe\Filter;
use Magento\Framework\Locale\ListsInterface;

class TimeFrames
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var ListsInterface
     */
    private $locale;

    /**

     * @param Filter         $filter
     * @param ListsInterface $locale
     */
    public function __construct(
        Filter $filter,
        ListsInterface $locale
    ) {
        $this->filter = $filter;
        $this->locale = $locale;
    }

    /**
     * @param $timeFrames
     *
     * @return array
     */
    public function handle($timeFrames)
    {
        $filteredTimeFrames = $this->filter->days($timeFrames);

        return array_map(function ($timeFrame) {
            $frames = $timeFrame->Timeframes;
            return $this->getTimeFrameOptions(
                $filterdTimeFrames,
                $frames->TimeframeTimeFrame,
                $timeFrame->Date
            );
        }, $filteredTimeFrames);
    }

    /**
     * @param $filterdTimeFrames
     * @param $timeFrames
     * @param $date
     *
     * @return array
     */
    private function getTimeFrameOptions(&$filterdTimeFrames, $timeFrames, $date)
    {
        //By adding the date to each timeframe option the filters are able to use the date associated with the timeframe
        foreach ($timeFrames as $timeFrame) {
            $timeFrame->Date = $date;
        }

        $timeFrames = $this->filter->options($timeFrames);

        foreach ($timeFrames as $timeFrame) {
            $options = $timeFrame->Options;
            $filterdTimeFrames[] = [
                'day'           => $this->getDayOfWeek($date),
                'from'          => $timeFrame->From,
                'from_friendly' => substr($timeFrame->From, 0, 5),
                'to'            => $timeFrame->To,
                'to_friendly'   => substr($timeFrame->To, 0, 5),
                'option'        => $options->validatedType ?? $options->string[0],
                'date'          => $date,
            ];
        }

        return $filterdTimeFrames;
    }

    /**
     * @param $date
     *
     * @return bool|string
     */
    private function getDayOfWeek($date)
    {
        $weekdays = $this->locale->getOptionWeekdays();

        return $weekdays[date('w', strtotime($date))]['label'];
    }
}
