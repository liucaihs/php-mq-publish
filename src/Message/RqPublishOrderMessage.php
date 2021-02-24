<?php
/**
 * 顺序消息
 * user: liucai
 * Date：2021/2/24
 * Time: 11:52
 */

namespace PangKu\RocketMQ\Message;


use MQ\Model\TopicMessage;

//顺序消息
class RqPublishOrderMessage implements RqMessage
{
    protected $topicMessage;

    public function getTopicMessage()
    {
        return $this->topicMessage;
    }

    /**
     * RqPublishOrderMessage constructor.
     * @param $messageBody 消息内容
     * @param $shardingKey 设置分区顺序KEY
     * @param $messageTag  Tag消息标签，二级消息类型
     * @param array $propertys 属性数组
     * @return TopicMessage
     */
    public function __construct($messageBody, $shardingKey, $messageTag, $propertys = [])
    {
        if (empty($messageBody)) {
            throw new \InvalidArgumentException('messageBody is not empty');
        }

        if (empty($shardingKey)) {
            throw new \InvalidArgumentException('shardingKey is not empty');
        }

        if (!is_array($propertys)) {
            throw new \InvalidArgumentException('the array initialize for Invalid propertys params');
        }

        $this->initMessage($messageBody, $shardingKey, $messageTag, $propertys);
    }

    /**
     * @param $messageBody 消息内容
     * @param $shardingKey 设置分区顺序KEY
     * @param $messageTag  Tag消息标签，二级消息类型
     * @param array $propertys 属性数组
     * @return TopicMessage
     */
    protected function initMessage($messageBody, $shardingKey, $messageTag, $propertys = [])
    {
        $publishMessage = new TopicMessage(
            $messageBody
        );
        // 设置属性
        if (!empty($propertys) && is_array($propertys)) {
            foreach ($propertys as $key => $value) {
                $publishMessage->putProperty($key, $value);
            }
        }

        // 设置分区顺序KEY
        $publishMessage->setShardingKey($shardingKey);
        //Tag消息标签，二级消息类型
        if (!empty($messageTag)) {
            $publishMessage->setMessageTag($messageTag);
        }

        $this->topicMessage = $publishMessage;
    }
}