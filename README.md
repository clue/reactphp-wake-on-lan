# clue/wol-react

Turn on your PC with [Wake-On-LAN](http://en.wikipedia.org/wiki/Wake-on-LAN) (WOL) requests

> WARNING: This is a pre-alpha version and there's a fair chance for it to not
work at all in your environment!

## Usage

Once [installed](#install), using this library is as simple as running:

```php
$loop = React\EventLoop\Factory::create();

$wol = new Wol($loop);
$wol->send('11:22:33:44:55:66');

$loop->run();
```

There's also a CLI script in `bin/wol.php` to send a WOL request from the 
command line simply by running:

```bash
$ php bin/wol.php 11:22:33:44:55:66
```

## Introduction

The following short introduction is mostly taken from wikipedia's
[article about WOL](http://en.wikipedia.org/wiki/Wake-on-LAN):

Wake-on-LAN ("WOL") is implemented using a specially designed packet called a
magic packet, which is sent to the computer to be woken up. The magic packet
contains the MAC address of the destination computer. Powered-down computers
capable of Wake-on-LAN will contain network devices able to "listen" to incoming
packets in low-power mode while the system is powered down. If a magic packet is
received that is directed to the device's MAC address, the NIC signals the
computer's power supply to initiate system wake-up, much in the same way as
pressing the power button would do.

The magic packet is a broadcast frame containing anywhere within its payload 6
bytes of all 255 (FF FF FF FF FF FF in hexadecimal), followed by sixteen
repetitions of the target computer's 48-bit MAC address, for a total of 102
bytes.

Since the magic packet is only scanned for the string above, and not actually
parsed by a full protocol stack, it may be sent as any network- and
transport-layer protocol, although this library uses a typical UDP datagram.
The magic packet is usually sent on the data link layer (layer 2 in the OSI
model) and when sent, is broadcast to all attached devices on a given network,
using the network broadcast address; the IP-address (layer 3 in the OSI model)
is not used.

## Install

The recommended way to install this library is [through composer](http://getcomposer.org). [New to composer?](http://getcomposer.org/doc/00-intro.md)

```JSON
{
    "require": {
        "clue/wol-react": "dev-master"
    }
}
```

## License

MIT

