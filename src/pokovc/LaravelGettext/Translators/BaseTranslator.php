<?php

namespace pokovc\LaravelGettext\Translators;

use pokovc\LaravelGettext\FileSystem;
use pokovc\LaravelGettext\Storages\Storage;
use pokovc\LaravelGettext\Config\Models\Config;
use pokovc\LaravelGettext\Adapters\AdapterInterface;
use pokovc\LaravelGettext\Exceptions\UndefinedDomainException;

abstract class BaseTranslator implements TranslatorInterface
{
    /**
     * Config container.
     *
     * @type \pokovc\LaravelGettext\Config\Models\Config
     */
    protected $configuration;

    /**
     * Framework adapter.
     *
     * @type \pokovc\LaravelGettext\Adapters\LaravelAdapter
     */
    protected $adapter;

    /**
     * File system helper.
     *
     * @var \pokovc\LaravelGettext\FileSystem
     */
    protected $fileSystem;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * Initializes the module translator.
     *
     * @param Config           $config
     * @param AdapterInterface $adapter
     * @param FileSystem       $fileSystem
     *
     * @param Storage $storage
     */
    public function __construct(
        Config $config,
        AdapterInterface $adapter,
        FileSystem $fileSystem,
        Storage $storage
    ) {
        // Sets the package configuration and session handler
        $this->configuration = $config;
        $this->adapter       = $adapter;
        $this->fileSystem    = $fileSystem;
        $this->storage       = $storage;
    }

    /**
     * Returns the current locale string identifier.
     *
     * @return String
     */
    public function getLocale()
    {
        return $this->storage->getLocale();
    }

    /**
     * Sets and stores on session the current locale code.
     *
     * @param $locale
     *
     * @return BaseTranslator
     */
    public function setLocale($locale)
    {
        if ($locale == $this->storage->getLocale()) {
            return $this;
        }

        $this->storage->setLocale($locale);

        return $this;
    }

    /**
     * Returns a boolean that indicates if $locale
     * is supported by configuration.
     *
     * @param $locale
     *
     * @return bool
     */
    public function isLocaleSupported($locale)
    {
        if ($locale) {
            return in_array($locale, $this->configuration->getSupportedLocales());
        }

        return false;
    }

    /**
     * Return the current locale.
     *
     * @return mixed
     */
    public function __toString()
    {
        return $this->getLocale();
    }

    /**
     * Gets the Current encoding.
     *
     * @return mixed
     */
    public function getEncoding()
    {
        return $this->storage->getEncoding();
    }

    /**
     * Sets the Current encoding.
     *
     * @param mixed $encoding the encoding
     *
     * @return self
     */
    public function setEncoding($encoding)
    {
        $this->storage->setEncoding($encoding);

        return $this;
    }

    /**
     * Sets the current domain and updates gettext domain application.
     *
     * @param String $domain
     *
     * @throws UndefinedDomainException If domain is not defined
     * @return self
     */
    public function setDomain($domain)
    {
        if (!in_array($domain, $this->configuration->getAllDomains())) {
            throw new UndefinedDomainException("Domain '$domain' is not registered.");
        }

        $this->storage->setDomain($domain);

        return $this;
    }

    /**
     * Returns the current domain.
     *
     * @return String
     */
    public function getDomain()
    {
        return $this->storage->getDomain();
    }

    /**
     * Returns supported locales.
     *
     * @return array
     */
    public function supportedLocales()
    {
        return $this->configuration->getSupportedLocales();
    }
}
