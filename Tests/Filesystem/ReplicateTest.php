<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\MediaBundle\Tests\Filesystem;

use PHPUnit\Framework\TestCase;
use Sonata\MediaBundle\Filesystem\Replicate;

class ReplicateTest extends TestCase
{
    public function testReplicate()
    {
        $master = $this->createMock('Gaufrette\Adapter');
        $slave = $this->createMock('Gaufrette\Adapter');
        $replicate = new Replicate($master, $slave);

        $master->expects($this->once())->method('mtime')->will($this->returnValue('master'));
        $slave->expects($this->never())->method('mtime');
        $this->assertSame('master', $replicate->mtime('foo'));

        $master->expects($this->once())->method('delete')->will($this->returnValue('master'));
        $slave->expects($this->once())->method('delete')->will($this->returnValue('master'));
        $replicate->delete('foo');

        $master->expects($this->once())->method('keys')->will($this->returnValue([]));
        $slave->expects($this->never())->method('keys')->will($this->returnValue([]));
        $this->assertInternalType('array', $replicate->keys());

        $master->expects($this->once())->method('exists')->will($this->returnValue(true));
        $slave->expects($this->never())->method('exists');
        $this->assertTrue($replicate->exists('foo'));

        $master->expects($this->once())->method('write')->will($this->returnValue(123));
        $slave->expects($this->once())->method('write')->will($this->returnValue(123));
        $this->assertSame(true, $replicate->write('foo', 'contents'));

        $master->expects($this->once())->method('read')->will($this->returnValue('master content'));
        $slave->expects($this->never())->method('read');
        $this->assertSame('master content', $replicate->read('foo'));

        $master->expects($this->once())->method('rename');
        $slave->expects($this->once())->method('rename');
        $replicate->rename('foo', 'bar');

        $master->expects($this->once())->method('isDirectory');
        $slave->expects($this->never())->method('isDirectory');
        $replicate->isDirectory('foo');
    }
}
