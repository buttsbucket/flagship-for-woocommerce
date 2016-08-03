<?php

class Flagship_Component
{
    protected $ctx;

    public function __construct(Flagship_Application $ctx)
    {
        $this->ctx = $ctx;

        $this->bootstrap();
    }

    public function bootstrap()
    {
    }

    protected function console($value)
    {
        $this->ctx['console']->log($value);
    }
}
