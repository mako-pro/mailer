<?php

namespace placer\mailer;

use mako\utility\Str;
use mako\syringe\Container;
use placer\mailer\drivers\UniMailer;

class Mailer
{
    /**
     * Container.
     *
     * @var \mako\syringe\Container
     */
    protected $container;

    /**
     * Mailer options.
     *
     * @var array
     */
    protected $options;

    /**
     * Mailer instance.
     *
     * @var \placer\mailer\drivers\UniMailer
     */
    protected $mailer;

    /**
     * Constructor
     *
     * @param Container  $container  IoC container instance
     */
    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->options = $container->get('config')->get('mailer::config');

        $this->mailer = $this->getMailerInstance();
    }

    /**
     * Configure named message type
     *
     * @param  string        $name  Message name
     * @param  array|object  $data  View data
     * @return $this
     */
    public function message(string $name = null, $data = null)
    {
        $config = $this->options['messages'][$name ?? 'default'];

        $this->mailer->setSubject($config['subject']);

        $view = $this->container->get('view');

        $this->mailer->view = $view->create($config['view']);

        if (! empty($data))
        {
            $this->mailer->view->assign('data', (object) ($data));
        }

        if (! empty($config['images']))
        {
            $i = 1;
            foreach ($config['images'] as $value)
            {
                $imageId = $this->mailer->addInlineImage($value);

                $this->mailer->view->assign('cid_'.$i, $imageId);

                $i++;
            }
        }

        if (! empty($config['attachments']))
        {
            foreach ($config['attachments'] as $value)
            {
                $this->mailer->addAttachment($value);
            }
        }

        return $this;
    }

    /**
     * Assign view variables
     *
     * @param   string  $name   Variable name
     * @param   mixed   $value  Variable value
     * @return  $this
     */
    public function with(string $name, $value)
    {
        $this->mailer->view->assign($name, $value);

        return $this;
    }

    /**
     * Attach file|files
     *
     * @param  string|array  $attachment  Path|paths to attachment file|files
     * @return $this
     */
    public function attach($attachment = null)
    {
        $this->mailer->clearAttachments();

        if (is_array($attachment))
        {
            foreach ($attachment as $value)
            {
                $this->mailer->addAttachment($value);
            }
        }
        else
        {
            $this->mailer->addAttachment($attachment);
        }

        return $this;
    }

    /**
     * Add recipient
     *
     * @param  string  $email  Recipient Email address
     * @param  string  $name   Recipient name
     * @return $this
     */
    public function to(string $email, string $name)
    {
        $textHtml = $this->mailer->view->render();

        $this->mailer->setTextHtml($textHtml);
        $this->mailer->setToEmail($email);
        $this->mailer->setToName($name);

        return $this;
    }

    /**
     * Send message
     *
     * @param   bool|boolean  $debug  Enable debug
     * @return  string  Message ID
     */
    public function send(bool $debug = false) : string
    {
        if ($debug === true)
        {
            $this->mailer->setDebugEnable();
        }

        if ($messageId = $this->mailer->sendMessage())
        {
            return $messageId;
        }

        return false;
    }

    /**
     * Get the mailer instance
     *
     * @return \placer\mailer\drivers\UniMailer
     */
    private function getMailerInstance()
    {
        $mailer = new UniMailer();

        $settings = $this->options['settings'];

        foreach ($settings as $key => $value)
        {
            $setter = 'set' . Str::underscored2camel($key);

            if (method_exists($mailer, $setter))
            {
                $mailer->{$setter}($value);
            }
        }

        return $mailer;
    }

}
