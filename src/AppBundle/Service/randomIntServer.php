<?php
namespace AppBundle\Service;

use Psr\Log\LoggerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;

class randomIntServer implements ConsumerInterface
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
            throw new \Exception("eeaaa");
        }
        self::$nb++;
        $body = unserialize($msg->body);
        print ".";
        return random_int($body['min'], $body['max']);
    }
}
