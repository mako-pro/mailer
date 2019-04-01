<?php

namespace placer\mailer;

use mako\application\Package;

class MailerPackage extends Package
{
    /**
     * Package name.
     *
     * @var string
     */
    protected $packageName = 'placer/mailer';

    /**
     * Package namespace.
     *
     * @var string
     */
    protected $fileNamespace = 'mailer';

    /**
     * {@inheritdoc}
     */
    protected function bootstrap(): void
    {
        $this->container->registerSingleton([Mailer::class, 'mailer'], function()
        {
            return new Mailer($this->container);
        });
    }

}
