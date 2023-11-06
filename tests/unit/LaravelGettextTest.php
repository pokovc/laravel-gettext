<?php

use \Mockery as m;
use pokovc\LaravelGettext\Adapters\AdapterInterface;
use pokovc\LaravelGettext\Storages\MemoryStorage;
use pokovc\LaravelGettext\Testing\Adapter\TestAdapter;
use pokovc\LaravelGettext\Testing\BaseTestCase;
use pokovc\LaravelGettext\Config\ConfigManager;
use pokovc\LaravelGettext\FileSystem;
use pokovc\LaravelGettext\Translators\Symfony;

class LaravelGettextTest extends BaseTestCase
{
    /**
     * Base app path.
     *
     * @var string
     */
    protected $appPath = __DIR__ . '/../../vendor/laravel/laravel/bootstrap/app.php';

    /**
     * @var Symfony
     */
    protected $translator;

    protected function setUp(): void
    {
        parent::setUp();
        $testConfig = include __DIR__ . '/../config/config.php';

        $config = ConfigManager::create($testConfig);
        $adapter = app($config->get()->getAdapter());
        $fileSystem = new FileSystem($config->get(), app_path(), storage_path());

        $translator = new Symfony(
            $config->get(),
            $adapter,
            $fileSystem,
            new MemoryStorage($config->get())
        );

        $this->translator = $translator;
    }

    public function testAdapter()
    {
        $testConfig = include __DIR__ . '/../config/config.php';
        $config = ConfigManager::create($testConfig);
        $adapter = app($config->get()->getAdapter());
        $this->assertInstanceOf(AdapterInterface::class, $adapter);
        $this->assertInstanceOf(TestAdapter::class, $adapter);
    }

    /**
     * Test setting locale.
     */
    public function testSetLocale()
    {
        $response = $this->translator->setLocale('en_US');

        $this->assertEquals('en_US', $response);
    }

    /**
     * Test getting locale.
     * It should receive locale from mocked config.
     */
    public function testGetLocale()
    {
        $response = $this->translator->getLocale();

        $this->assertEquals('en_US', $response);
    }

    public function testIsLocaleSupported()
    {
        $this->assertTrue($this->translator->isLocaleSupported('en_US'));
    }

    /**
     * Test dumping locale to string.
     */
    public function testToString()
    {
        $response = $this->translator->__toString();

        $this->assertEquals('en_US', $response);
    }

    public function testGetEncoding()
    {
        $response = $this->translator->getEncoding();
        $this->assertNotEmpty($response);
        $this->assertEquals('UTF-8', $response);
    }

    public function testSetEncoding()
    {
        $response = $this->translator->setEncoding('UTF-8');
        $this->assertNotEmpty($response);
        $this->assertInstanceOf('pokovc\LaravelGettext\Translators\Symfony', $response);
    }

    protected function tearDown(): void
    {
        m::close();
    }
}
