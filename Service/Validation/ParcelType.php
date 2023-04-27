<?php

namespace TIG\PostNL\Service\Validation;

class ParcelType implements ContractInterface
{
    /**
     * Validate the data. Returns false when the
     *
     * @param $line
     *
     * @return bool|mixed
     */
    // @codingStandardsIgnoreStart
    public function validate($line)
    {
        $line = strtolower($line);

        if ($this->isWildcard($line)) {
            return '*';
        }

        if ($this->isRegular($line)) {
            return 'regular';
        }

        if ($this->isExtraAtHome($line)) {
            return 'extra@home';
        }

        if ($this->isPakjegemak($line)) {
            return 'pakjegemak';
        }

        if ($this->isLetterboxPackage($line)) {
            return 'letterbox_package';
        }

        if ($this->isBoxablePacket($line)) {
            return 'boxable_packets';
        }

        return false;
    }
    // @codingStandardsIgnoreEnd

    /**
     * @param $line
     *
     * @return bool
     */
    private function isWildcard($line)
    {
        return in_array($line, ['', '0', '*']);
    }

    /**
     * @param $line
     *
     * @return bool
     */
    private function isRegular($line)
    {
        return in_array($line, ['pakket', 'regular']);
    }

    /**
     * @param $line
     *
     * @return bool
     */
    private function isExtraAtHome($line)
    {
        return in_array($line, ['extra@home', 'extra @ home', 'extra_@_home', 'extraathome', 'extra_at_home']);
    }

    /**
     * @param $line
     *
     * @return bool
     */
    private function isPakjegemak($line)
    {
        $options = ['pakjegemak', 'pakje_gemak', 'pakje gemak', 'PakjeGemak', 'postkantoor', 'post office'];

        return in_array($line, $options);
    }

    /**
     * @param $line
     *
     * @return bool
     */
    private function isLetterboxPackage($line)
    {
        return in_array($line, ['letterbox_package']);
    }

    /**
     * @param $line
     *
     * @return bool
     */
    private function isBoxablePacket($line)
    {
        return in_array($line, ['boxable_packets']);
    }
}
