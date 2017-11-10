<?php
namespace AppBundle\Service;

use Psr\Log\LoggerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;

class testEvent implements ConsumerInterface
{
    private $logger;
    private static $nb=0;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public function execute(AMQPMessage $msg)
    {
        if (random_int(0, 10)==5) {
            print "B";
            return false;
        }
        self::$nb++;
        $body = unserialize($msg->body);
        print ".";
        file_put_contents('/tmp/test.txt', sprintf("batchid:%s,i:%s\n",$body['batchid'],$body['user_id']),FILE_APPEND);
        return true;
    }
}