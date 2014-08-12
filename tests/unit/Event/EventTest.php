<?php

/*
 * This file is part of the Indigo Supervisor package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Unit;

use Indigo\Supervisor\Event\Event;
use Codeception\TestCase\Test;

/**
 * Tests for Event
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @coversDefaultClass Indigo\Supervisor\Event\Event
 * @group              Supervisor
 * @group              Event
 */
class EventTest extends Test
{
    protected $event;

    protected $header = array(
        'ver'        => '3.0',
        'server'     => 'supervisor',
        'serial'     => '21',
        'pool'       => 'listener',
        'poolserial' => '10',
        'eventname'  => 'PROCESS_COMMUNICATION_STDOUT',
        'len'        => '54',
    );

    protected $payload = array(
        'process_name' => 'foo',
        'group_name'   => 'bar',
        'pid'          => '123',
    );

    protected $body = 'This is the data that was sent between the tags';

    public function _before()
    {
        $this->event = new Event($this->header, $this->payload, $this->body);
    }

    /**
     * @covers ::getHeader
     * @covers ::setHeader
     * @covers ::getPayload
     * @covers ::setPayload
     * @covers ::getBody
     * @covers ::setBody
     */
    public function testGetSet()
    {
        $this->assertNull($this->event->getHeader('fake'));
        $this->assertEquals($this->header['ver'], $this->event->getHeader('ver'));
        $this->assertEquals('fake', $this->event->getHeader('fake', 'fake'));
        $this->assertInstanceOf(
            get_class($this->event),
            $this->event->setHeader($this->header)
        );
        $this->assertEquals($this->header, $this->event->getHeader());


        $this->assertNull($this->event->getPayload('fake'));
        $this->assertEquals($this->payload['pid'], $this->event->getPayload('pid'));
        $this->assertEquals('fake', $this->event->getPayload('fake', 'fake'));
        $this->assertInstanceOf(
            get_class($this->event),
            $this->event->setPayload($this->payload)
        );
        $this->assertEquals($this->payload, $this->event->getPayload());


        $this->assertInstanceOf(
            get_class($this->event),
            $this->event->setBody($this->body)
        );
        $this->assertEquals($this->body, $this->event->getBody());
    }
}
