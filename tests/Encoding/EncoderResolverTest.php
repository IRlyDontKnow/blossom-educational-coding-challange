<?php

namespace Blossom\BackendDeveloperTest\Tests\Encoding;

use Blossom\BackendDeveloperTest\Encoding\EncoderResolver;
use Blossom\BackendDeveloperTest\Encoding\Encoders\EncoderInterface;
use Blossom\BackendDeveloperTest\Encoding\Exceptions\FailedToResolveEncoder;
use PHPUnit\Framework\TestCase;

class EncoderResolverTest extends TestCase
{
    public function testThrowsFailedToResolveEncoderForNotRegisteredFormat()
    {
        $encoderResolver = new EncoderResolver([]);
        $this->expectException(FailedToResolveEncoder::class);

        $encoderResolver->resolve('avi');
    }

    public function testResolveEncoder()
    {
        $encoderResolver = new EncoderResolver([
            'avi' => $this->createMock(EncoderInterface::class),
        ]);
        $encoder = $encoderResolver->resolve('avi');
        $this->assertInstanceOf(EncoderInterface::class, $encoder);
    }
}
