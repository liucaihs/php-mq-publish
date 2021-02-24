
预发配置和正式配置待定

2.发布顺序消息

文档:https://help.aliyun.com/document_detail/141783.html?spm=a2c4g.11186623.6.639.1dbf7b5bpCsgbd



3.封装sdk要求

参考lixiaohong/tyrex

发布结构:
$pkOrderProducer = new PkPublish($config);

$rocketmqPublishMessage = new RqPublishMessage(xx,xxx,....);

rocketmqPublishMessage->setLogHandler(xxxx);

$topicMessage = $pkOrderProducer->publishMessage($rocketmqPublishMessage);

支持业务系统设置日志处理

了解一下monolog/monolog

sdk库的name pangku/php-mq-publish

```
use PangKu\RocketMQ\PkPublish;
use PangKu\RocketMQ\Message\RqPublishOrderMessage;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$config = [
    "account_endpoint" => "http://xxxxx.aliyuncs.com",
    "access_id" => "xxXXWw",
    "access_key" => "xx9DQy",
    "instance_id" => "xxxBXgBYndb",
    "topic" => "local-partition-test",
    "tag" => "business-test",
];

// create a log channel
$log = new Logger('name');
$log->pushHandler(new StreamHandler('D:/tmp/RocketMQ.log', Logger::WARNING));

$pkOrderProducer = new PkPublish($config);
$pkOrderProducer->setLogHandler($log, true);
$rocketmqPublishMessage = new RqPublishOrderMessage("xxxxxxxyyyyyyyyy", "xxxxxx", $config["tag"], ["a" => 12]);
$topicMessage = $pkOrderProducer->publishMessage($rocketmqPublishMessage);
```