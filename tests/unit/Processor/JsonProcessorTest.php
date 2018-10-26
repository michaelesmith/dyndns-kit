<?php

namespace DynDNSKit\Tests\Unit\Processor;

use DynDNSKit\Processor\JsonProcessor;
use DynDNSKit\Processor\ProcessorException;
use DynDNSKit\Query;
use DynDNSKit\Tests\Common\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;

class JsonProcessorTest extends TestCase
{
    const FILENAME = '/var/data/dns.json';

    public function dpTestProcess()
    {
        return [
            0 => [
                function () {
                    $fs = vfsStream::setup();

                    return $fs;
                }, // $filesystem
            ], // new file created
            1 => [
                function () {
                    $fs = vfsStream::setup('root', null, [
                        'var' => [
                            'data' => [
                                'dns.json' => '{"test1.myhost.com":"192.168.1.2","test2.myhost.com":"192.168.1.2"}'
                            ],
                        ],
                    ]);

                    return $fs;
                }, // $filesystem
            ], // existing file updated
            2 => [
                function () {
                    $fs = vfsStream::setup('root', null, [
                        'var' => [
                            'data' => [
                                'dns.json' => '{"test1.myhost.com":"192.168.1.2","test2.myhost.com":"192.168.1.2'
                            ],
                        ],
                    ]);

                    return $fs;
                }, // $filesystem
                ProcessorException::class, // $exceptionClass
                '/The file ".+" does not appear to be valid json/', // $exceptionMessage
            ], // bad json
        ];
    }

    /**
     * @dataProvider dpTestProcess
     */
    public function testProcess($filesystem, $exceptionClass = null, $exceptionMessage = null)
    {
        $query = new Query($ip = '192.168.1.1', [$host1 = 'test1.myhost.com', $host2 = 'test2.myhost.com']);

        $filesystem = $filesystem();
        /** @var vfsStreamDirectory $filesystem */
        $sut = new JsonProcessor($filesystem->url() . self::FILENAME);

        if ($exceptionClass) {
            $this->expectException($exceptionClass);
            if ($exceptionMessage) {
                $this->expectExceptionMessageRegExp($exceptionMessage);
            }
        }

        $this->assertTrue($sut->process($query));

        $this->assertTrue($filesystem->hasChild('root' . self::FILENAME));
        $content = $filesystem->getChild('root' . self::FILENAME)->getContent();
        $this->assertContains($host1, $content);
        $this->assertContains($host2, $content);
        $this->assertContains($ip, $content);
    }
}
