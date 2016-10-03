<?php

namespace FS\Configurations;

class WordPress implements \FS\Components\Factory\ConfigurationInterface
{
    public function getOptions()
    {
        $options = new \FS\Components\Options();
        $options->setWpOptionName('woocommerce_flagship_shipping_method_settings');

        return $options;
    }

    public function getSettings()
    {
        $settings = new \FS\Components\Settings();

        $settings['FLAGSHIP_SHIPPING_PLUGIN_DEBUG'] = false;
        $settings['FLAGSHIP_SHIPPING_PLUGIN_ID'] = 'flagship_shipping_method';
        $settings['FLAGSHIP_SHIPPING_API_ENTRY_POINT'] = $settings['FLAGSHIP_SHIPPING_PLUGIN_DEBUG'] ? 'http://127.0.0.1:3002' : 'https://api.smartship.io';
        $settings['FLAGSHIP_SHIPPING_API_TIMEOUT'] = 14;
        $settings['FLAGSHIP_FOR_WOOCOMMERCE_VERSION'] = FLAGSHIP_SHIPPING_PLUGIN_VERSION;

        return $settings;
    }

    public function getDebugger()
    {
        return new \FS\Components\Debugger();
    }

    public function getRequestParam()
    {
        return new \FS\Components\Web\RequestParam();
    }

    public function getNotifier()
    {
        $notifier = new \FS\Components\Notifier();

        $notifier->setViewer($this->getViewer());

        return $notifier;
    }

    public function getViewer()
    {
        return new \FS\Components\Viewer();
    }

    public function getHtml()
    {
        return new \FS\Components\Html();
    }

    public function getUrl()
    {
        return new \FS\Components\Url();
    }

    public function getHookManager()
    {
        $manager = new \FS\Components\Hook\HookManager();

        return $manager;
    }

    public function getClient()
    {
        $client = new \FS\Components\Http\Client();

        $settings = $this->getSettings();

        $client->setTimeout($settings['FLAGSHIP_SHIPPING_API_TIMEOUT']);
        $client->setEntryPoint($settings['FLAGSHIP_SHIPPING_API_ENTRY_POINT']);
        $client->setRequestRunner($this->getRequestRunner());

        return $client;
    }

    public function getRequestRunner()
    {
        $requestRunner = new \FS\Components\Http\RequestRunner\RequestRunner();
        $requestRunner->setRequestRunnerDriver(new \FS\Components\Http\RequestRunner\Drivers\WordPress());

        return $requestRunner;
    }

    public function getRequestBuilderFactory()
    {
        $factory = new \FS\Components\Shipping\RequestBuilder\Factory\RequestBuilderFactory();

        $factory->setFactoryDriver(new \FS\Components\Shipping\RequestBuilder\Drivers\WordPress\FactoryDriver());

        return $factory;
    }

    public function getRateProcessor()
    {
        return new \FS\Components\Shipping\RateProcessor();
    }
}
