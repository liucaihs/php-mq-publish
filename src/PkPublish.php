<?php
/**
 * user: liucai
 * Date：2021/2/24
 * Time: 11:43
 */

namespace PangKu\RocketMQ;


use MQ\Model\TopicMessage;
use MQ\MQClient;

class PkPublish
{
    private $client;
    private $producer;

    private $config;
    private $configRequire = ["account_endpoint", "access_id", "access_key", "topic", "instance_id"];

    private $log = null;

    public function __construct($config)
    {
        if (!is_array($config)) {
            throw new \InvalidArgumentException('the array initialize for Invalid params');
        }
        foreach ($this->configRequire as $rKey) {
            if (empty($config[$rKey])) {
                throw new \InvalidArgumentException('配置 ' . $rKey . ' is not empty');
            }
        }

        $this->config = $config;
        $this->client = new MQClient(
        // 设置HTTP接入域名（此处以公共云生产环境为例）
            $this->config["account_endpoint"],
            // AccessKey 阿里云身份验证，在阿里云服务器管理控制台创建
            $this->config["access_id"],
            // SecretKey 阿里云身份验证，在阿里云服务器管理控制台创建
            $this->config["access_key"]
        );

        // 所属的 Topic
        $topic = $this->config["topic"];
        // Topic所属实例ID，默认实例为空NULL
        $instanceId = $this->config["instance_id"];

        $this->producer = $this->client->getProducer($instanceId, $topic);
    }

    public function setLogHandler()
    {

    }

    public function publishMessage(TopicMessage $publishMessage)
    {
        try {

            $result = $this->producer->publishMessage($publishMessage);
            print $this->config["tag"] . " Send mq message success. msgId is:" . $result->getMessageId() . ", bodyMD5 is:" . $result->getMessageBodyMD5() . "\n";

        } catch (\Exception $e) {
            print_r($e->getMessage() . "\n");
        }
    }

    protected function logErro($data)
    {

    }

    protected function logSuccess($data)
    {

    }
}