
预发配置和正式配置待定

2.发布顺序消息

文档:https://help.aliyun.com/document_detail/141783.html?spm=a2c4g.11186623.6.639.1dbf7b5bpCsgbd



3.封装sdk要求



发布结构:
$pkOrderProducer = new PkPublish($config);

$rocketmqPublishMessage = new RqPublishMessage(xx,xxx,....);

rocketmqPublishMessage->setLogHandler(xxxx);

$topicMessage = $pkOrderProducer->publishMessage($rocketmqPublishMessage);

支持业务系统设置日志处理

了解一下monolog/monolog

sdk库的name p/php-mq-publish
