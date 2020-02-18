<?php
/**
 * Copyright (c) 2020 Wakers.cz
 * @author Jiří Zapletal (https://www.wakers.cz, zapletal@wakers.cz)
 */
declare(strict_types=1);

namespace Wakers\ConversionModule\Manager;

use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Nette\Utils\DateTime;
use Nette\Utils\Json;
use Wakers\BaseModule\Database\AbstractDatabase;
use Wakers\ConversionModule\Database\Conversion;

class ConversionManager extends AbstractDatabase
{
    /**
     * @var array
     */
    protected $smtp;

    /**
     * ConversionManager constructor.
     * @param array $smtp
     */
    public function __construct(array $smtp)
    {
        $this->smtp = $smtp;
    }

    /**
     * @param string $conversionName
     * @param DateTime $createdAt
     * @param array $values
     * @return Conversion
     * @throws \Nette\Utils\JsonException
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function create(string $conversionName, DateTime $createdAt, array $values) : Conversion
    {
        $json = Json::encode($values);

        $conversion = new Conversion;
        $conversion->setName($conversionName);
        $conversion->setCreatedAt($createdAt);
        $conversion->setParams($json);
        $conversion->save();

        return $conversion;
    }

    /**
     * @param array $values
     * @param string $subject
     * @param string $messageBody
     */
    public function sendMail(array $values, string $subject, string $messageBody) : void
    {
        $sender = $this->smtp['sender'];

        $message = new Message;
        $message->setSubject($subject);
        $message->setFrom($sender['email'], $sender['name']);
        $message->addTo($sender['to']);
        foreach ($sender['bcc'] as $bcc) {
            $message->addBcc($bcc);
        }

        $messageBody .= "\n\r\n\r";
        foreach ($values as $key => $value) {
            $messageBody .= "{$key}: {$value}\n\r";
        }
        $message->setBody($messageBody);

        $smtp = new SmtpMailer($this->smtp['config']);
        $smtp->send($message);
    }
}
