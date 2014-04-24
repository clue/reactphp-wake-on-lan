<?php

use Evenement\EventEmitter;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use Socket\React\Datagram\Factory as DatagramFactory;
use \InvalidArgumentException;

class Wol extends EventEmitter
{
    private $socket;
    private $address = '255.255.255.255:7';
    
    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }
    
    public function send($mac, $address = null)
    {
        if ($address === null) {
            $address = $this->address;
        }

        $mac = $this->coerceMac($mac);
        
        $message = "\xFF\xFF\xFF\xFF\xFF\xFF" . str_repeat($this->formatMac($mac), 16);
        $deferred = new Deferred();

        $factory = new DatagramFactory($this->loop);
        $factory->createClient($address, array(
            'broadcast' => true,
        ))->then(function($socket) use ($message, $deferred) {
            $socket->send($message);
            $socket->end();
            $deferred->resolve();
        }, function($e) use ($deferred) {
            $deferred->reject($e);
        });

        return $deferred->promise();
    }
    
    public function handleMessage($message, $remote)
    {
        var_dump('received', $message, 'from', $remote);
        
        // $mac = $this->parseMessage($message);
        // $this->emit('message', array($mac, $message));
    }
    
    public function pause()
    {
        $this->socket->pause();
    }
    
    public function resume()
    {
        $this->socket->resume();
    }
    
    /**
     * 
     * @param string $mac mixed case mac address with colon, hyphen or no separators
     * @return string uppercase mac address with colon separators (e.g. 00:11:22:33:44:55)
     * @throws InvalidArgumentException
     */
    public function coerceMac($mac)
    {
        if (strlen($mac) === 12) {
            // no separators => add colons in between
            $mac = implode(':', str_split($mac, 2));
        } else if(strpos($mac, '-') !== false) {
            // hyphen separators => replace with colons
            $mac = str_replace('-', ':', $mac);
        }
        $mac = strtoupper($mac);
        
        if (!preg_match('/(?:[A-F0-9]{2}\:){5}[A-F0-9]{2}/',$mac)) {
            throw new InvalidArgumentException('Invalid mac address given');
        }
        
        return $mac;
    }
    
    private function formatMac($mac)
    {
        $address = '';
        
        foreach (explode(':', $mac) as $part) {
            $address .= chr(hexdec($part));
        }
        
        return $address;
    }
}
