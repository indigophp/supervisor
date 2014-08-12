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

use Codeception\TestCase\Test;

/**
 * Tests for Listeners
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * @coversDefaultClass Indigo\Supervisor\Event\Event
 * @group              Supervisor
 * @group              Listener
 */
abstract class AbstractListenerTest extends Test
{
    protected $listener;
    protected $input;
    protected $output;

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

    protected function regenerate(&$input, &$output)
    {
        $input  = @fopen('php://temp', 'r+');
        $output = @fopen('php://temp', 'w+');

        $this->listener->setInputStream($input)->setOutputStream($output);
    }

    /**
     * @covers ::getInputStream
     * @covers ::setInputStream
     * @covers ::getOutputStream
     * @covers ::setOutputStream
     */
    public function testStreams()
    {
        $this->regenerate($input, $output);

        $this->assertInstanceOf(
            get_class($this->listener),
            $this->listener->setInputStream($input)
        );

        $this->assertInstanceOf(
            get_class($this->listener),
            $this->listener->setOutputStream($output)
        );

        $this->assertTrue(is_resource($this->listener->getInputStream()));
        $this->assertTrue(is_resource($this->listener->getOutputStream()));
    }

    /**
     * @covers            ::setInputStream
     * @expectedException InvalidArgumentException
     */
    public function testInputStreamFailure()
    {
        $this->listener->setInputStream(false);
    }

    /**
     * @covers            ::setOutputStream
     * @expectedException InvalidArgumentException
     */
    public function testOutputStreamFailure()
    {
        $this->listener->setOutputStream(false);
    }

    /**
     * @covers ::processResult
     */
    public function testProcessResult()
    {
        $method = new \ReflectionMethod(get_class($this->listener), 'processResult');
        $method->setAccessible(true);

        $this->regenerate($input, $output);

        $method->invoke($this->listener, 0);
        rewind($output);
        $this->assertEquals("RESULT 2\n", fgets($output));
        $this->assertEquals("OK", fgets($output));

        $this->regenerate($input, $output);

        $method->invoke($this->listener, 1);
        rewind($output);
        $this->assertEquals("RESULT 4\n", fgets($output));
        $this->assertEquals("FAIL", fgets($output));

        $this->assertFalse($method->invoke($this->listener, 3));
    }
}
