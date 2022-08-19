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
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Model\Mail\Template;

use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MessageInterfaceFactory;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePart;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /** @var Part  */
    private $part;

    /**
     * @param FactoryInterface                  $templateFactory
     * @param MessageInterface                  $message
     * @param SenderResolverInterface           $senderResolver
     * @param ObjectManagerInterface            $objectManager
     * @param TransportInterfaceFactory         $mailTransportFactory
     * @param MessageInterfaceFactory|null      $messageFactory
     * @param EmailMessageInterfaceFactory|null $emailMessageInterfaceFactory
     * @param MimeMessageInterfaceFactory|null  $mimeMessageInterfaceFactory
     * @param MimePartInterfaceFactory|null     $mimePartInterfaceFactory
     * @param AddressConverter|null             $addressConverter
     * @param Part                              $part
     */
    public function __construct(
        FactoryInterface             $templateFactory,
        MessageInterface             $message,
        SenderResolverInterface      $senderResolver,
        ObjectManagerInterface       $objectManager,
        TransportInterfaceFactory    $mailTransportFactory,
        MessageInterfaceFactory      $messageFactory = null,
        EmailMessageInterfaceFactory $emailMessageInterfaceFactory = null,
        MimeMessageInterfaceFactory  $mimeMessageInterfaceFactory = null,
        MimePartInterfaceFactory     $mimePartInterfaceFactory = null,
        AddressConverter             $addressConverter = null,
        Part                         $part
    ) {
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory,
            $messageFactory,
            $emailMessageInterfaceFactory,
            $mimeMessageInterfaceFactory,
            $mimePartInterfaceFactory,
            $addressConverter
        );

        $this->part = $part;
    }

    public function addAttachment($content, $fileName)
    {
        $attachmentPart = new Part($content);
        $attachmentPart->setType('application/pdf')
                       ->setFileName($fileName)
                       ->setDisposition(Mime::DISPOSITION_ATTACHMENT)
                       ->setEncoding(Mime::ENCODING_BASE64)
                       ->setCharset('utf-8');

        return $attachmentPart;
    }
}
