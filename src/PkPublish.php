<?php
/**
 * user: liucai
 * Date：2021/2/24
 * Time: 11:43
 */

namespace PangKu\RocketMQ;

use MQ\MQClient;
use PangKu\RocketMQ\Message\RqMessage;

class PkPublish
{
    private $client;
    private $producer;

    private $config;
    private $configRequire = ["account_endpoint", "access_id", "access_key", "topic", "instance_id"];

    private $log = null;
    private $logSuccess = false;

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

    public function setLogHandler($log, $logSuccess = false)
    {
        $this->log = $log;
        $this->logSuccess = $logSuccess;
    }

    public function publishMessage(RqMessage $publishMessage)
    {
        $logData = [];

        try {
            $topicMessage = $publishMessage->getTopicMessage();

            $logData['messageBody'] = $topicMessage->getMessageBody();
            $logData['messageTag'] = $topicMessage->getMessageTag();
            $logData['properties'] = $topicMessage->getProperties();

            $result = $this->producer->publishMessage($topicMessage);

            $logData["extra"] = [
                "text" => "Send success. msgId is:" . $result->getMessageId() . ", bodyMD5 is:" . $result->getMessageBodyMD5(),
            ];
            $logData["msgId"] = $result->getMessageId();
            $logData["bodyMD5"] = $result->getMessageBodyMD5();
            $this->logRecord($logData);

        } catch (\Exception $e) {
//            print_r($e->getMessage() . "\n");
            throw new \Exception("RocketMQ 发布消息错误 " . $e->getMessage());

        }
    }


    protected function logRecord($data)
    {
        if (!empty($this->log) && !empty($data)) {
            if (empty($data["msgId"]) || empty($data["bodyMD5"])) {
                $data["result"] = "发布消息错误";
                $this->log->error(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            } elseif ($this->logSuccess && !empty($data["msgId"]) && !empty($data["bodyMD5"])) {
                $data["result"] = "发布消息成功";
                $this->log->info(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            }
        }
    }
}