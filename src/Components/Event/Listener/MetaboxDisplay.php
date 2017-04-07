<?php

namespace FS\Components\Event\Listener;

use FS\Components\AbstractComponent;
use FS\Context\ApplicationListenerInterface;
use FS\Components\Event\NativeHookInterface;
use FS\Context\ConfigurableApplicationContextInterface as Context;
use FS\Context\ApplicationEventInterface as Event;
use FS\Components\Event\ApplicationEvent;

class MetaboxDisplay extends AbstractComponent implements ApplicationListenerInterface, NativeHookInterface
{
    public function getSupportedEvent()
    {
        return ApplicationEvent::METABOX_DISPLAY;
    }

    public function onApplicationEvent(Event $event, Context $context)
    {
        $order = $event->getInput('order');

        $context
            ->controller('\\FS\\Components\\Shipping\\Controller\\MetaboxController', [
                'metabox-build' => 'display',
            ])
            ->before(function ($context) use ($order) {
                // apply middlware function before invoke controller method
                $context
                    ->_('\\FS\\Components\\Notifier')
                    ->scope('shop_order', ['id' => $order->getId()]);
            })
            ->after(function ($context) {
                // as we are in metabox,
                // we have to explicit "show" notification
                // why? wordpress will render shop order after it dealt with any POST request to shop order
                // any alerts added previously (treating POST data) will be shown here
                $context
                    ->_('\\FS\\Components\\Notifier')
                    ->view();
            })
            ->dispatch('metabox-build', [$order]);
    }

    public function publishNativeHook(Context $context)
    {
        \add_action('add_meta_boxes', function () use ($context) {
            \add_meta_box(
                'wc-flagship-shipping-box',
                __('FlagShip', FLAGSHIP_SHIPPING_TEXT_DOMAIN),
                function ($postId, $post) use ($context) {
                    $event = new ApplicationEvent(ApplicationEvent::METABOX_DISPLAY);

                    $order = $context->_('\\FS\\Components\\Shop\\Factory\\ShopFactory')->resolve('order', array(
                        'id' => $postId,
                    ));

                    $event->setInputs([
                        'order' => $order,
                    ]);

                    $context->publishEvent($event);
                },
                'shop_order',
                'side',
                'high'
            );
        });

        return $this;
    }

    public function getNativeHookType()
    {
        return self::TYPE_ACTION;
    }
}
