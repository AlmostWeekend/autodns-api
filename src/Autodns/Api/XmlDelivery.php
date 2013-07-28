<?php

namespace Autodns\Api;

use Autodns\Api\Account\Info;
use Tool\ArrayToXmlConverter;
use Tool\XmlToArrayConverter;
use Buzz\Browser;
use Buzz\Message\MessageInterface;

class XmlDelivery
{
    /**
     * @var \Tool\ArrayToXmlConverter
     */
    private $arrayXmlConverter;

    /**
     * @var \Autodns\Api\Account\Info
     */
    private $accountInfo;

    /**
     * @var \Buzz\Browser
     */
    private $sender;

    /**
     * @var \Tool\XmlToArrayConverter
     */
    private $xmlToArrayConverter;

    public function __construct(
        ArrayToXmlConverter $arrayXmlConverter,
        Info $accountInfo,
        Browser $sender,
        XmlToArrayConverter $xmlToArrayConverter
    )
    {
        $this->arrayXmlConverter = $arrayXmlConverter;
        $this->accountInfo = $accountInfo;
        $this->sender = $sender;
        $this->xmlToArrayConverter = $xmlToArrayConverter;
    }

    /**
     * @param string $url
     * @param array $task
     * @return string
     */
    public function send($url, array $task)
    {
        $request = $this->buildRequest($task);

        $xml = $this->arrayXmlConverter->convert($request);

        $response = $this->sender->post($url, array(), $xml);

        return $this->xmlToArrayConverter->convert($response->getContent());
    }

    private function buildRequest($task)
    {
        return array(
            'auth' => array(
                'user' => $this->accountInfo->getUsername(),
                'password' => $this->accountInfo->getPassword(),
                'context' => $this->accountInfo->getContext()
            ),
            'task' => $task
        );
    }
}