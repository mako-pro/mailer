<?php

namespace placer\mailer\drivers;

use Exception;
use placer\mailer\helpers\MIMEtypes;

class UniMailer
{
    /**
     * Line wrapping limit
     *
     * @var integer
     */
    const WRAP_LEN = 76;

    /**
     * Line length limit
     *
     * @var integer
     */
    const LINE_LEN_MAX = 998;

    /**
     * Max length of multibyte segment
     *
     * @var string
     */
    const MB_LEN_MAX = 7;

    /**
     * End of the line
     *
     * @var string
     */
    const CRLF = "\r\n";

    /**
     * Charset of the message
     *
     * @var string
     */
    const CHARSET = 'utf-8';

    /**
     * Hostname
     *
     * @var string
     */
    private $hostName = '';

    /**
     * Method ('mail', 'smtp')
     *
     * @var string
     */
    private $mailMethod = 'smtp';

    /**
     * SMTP server
     *
     * @var string
     */
    private $SMTPserver = 'localhost';

    /**
     * SMTP port
     *
     * @var integer
     */
    private $SMTPport = 25;

    /**
     * SMTP username
     *
     * @var string
     */
    private $SMTPusername = '';

    /**
     * SMTP password
     *
     * @var string
     */
    private $SMTPpassword = '';

    /**
     * Secure SMTP connection
     *
     * @var boolean
     */
    private $forceSMTPsecure = false;

    /**
     * Sender name
     *
     * @var string
     */
    private $fromName = '';

    /**
     * Sender Email address

     * @var string
     */
    private $fromEmail = '';

    /**
     * SMTP timeout
     *
     * @var integer
     */
    private $SMTPtimeout = 30;

    /**
     * SMTP time limit
     *
     * @var integer
     */
    private $SMTPtimeLimit = 30;

    /**
     * Path to CA certificate file
     *
     * @var string
     */
    private $CAfile = '';

    /**
     * Recipient name
     *
     * @var string
     */
    private $toName;

    /**
     * Recipeint Email address
     *
     * @var string
     */
    private $toEmail;

    /**
     * Message subject
     *
     * @var string
     */
    private $subject;

    /**
     * Plain message text
     *
     * @var string
     */
    private $textPlain;

    /**
     * Html message text
     *
     * @var string
     */
    private $textHtml;

    /**
     * Encoding method ('base64' or 'quoted-printable')
     *
     * @var string
     */
    private $textEncoding = 'base64';

    /**
     * Language for message header ('en-us', ...)
     *
     * @var string
     */
    private $textContentLanguage;

    /**
     * Custom headers
     *
     * @var array
     */
    private $customHeaders = [];

    /**
     * Message ID
     *
     * @var string
     */
    private $messageId;

    /**
     * Boundaries string
     *
     * @var string
     */
    private $boundStr;

    /**
     * Message boundaries
     *
     * @var array
     */
    private $boundary;

    /**
     * Inline images
     *
     * @var array
     */
    private $inlineImages = [];

    /**
     * Inline images array key
     *
     * @var integer
     */
    private $inlineImagesKey = 0;

    /**
     * Attachments
     *
     * @var array
     */
    private $attachments = [];

    /**
     * Attachments array key
     *
     * @var integer
     */
    private $attachmentsKey = 0;

    /**
     * MIME headers
     *
     * @var array
     */
    private $mimeHeaders = [];

    /**
     * MIME body
     *
     * @var string
     */
    private $mimeBody = '';

    /**
     * Complete message string (headers + body)
     *
     * @var string
     */
    private $mailString = '';

    /**
     * SMTP socket
     *
     * @var null
     */
    private $SMTPsocket = null;

    /**
     * Supported extensions
     *
     * @var array
     */
    private $SMTPextensions = [];

    /**
     * Epoch connection flag
     *
     * @var null
     */
    private $epochConnectionOpened = null;

    /**
     * Counter success connrctions
     *
     * @var integer
     */
    private $counterSuccess = 0;

    /**
     * Last reply
     *
     * @var string
     */
    private $lastReply = '';

    /**
     * Enable debugging
     *
     * @var boolean
     */
    private $debugEnable = false;

    /**
     * Debugging configuration
     *
     * @var array
     */
    private $debug =
    [
        'method'      => 'log',                                       // Debug method (echo|log)
        'directory'   => MAKO_APPLICATION_PATH . '/storage/mailer/',  // Path to log file
        'file_name'   => 'UniMailer.log',                             // Log file name
        'time_offset' => 0,                                           // Server timezone offset from GMT (seconds)
    ];

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        // Without logic
    }

    /**
     * Set hostname for SMTP EHLO and message ID
     *
     * @param string $value Hostname
     */
    public function setHostName(string $value)
    {
        $this->hostName = $value ?: $this->getHostName();
    }

    /**
     * Set mail method (mail|smtp)
     *
     * @param string $value
     */
    public function setMailMethod(string $value)
    {
        $this->mailMethod = $value;
    }

    /**
     * Set SMTP server host name
     *
     * @param string $value
     */
    public function setSmtpServer(string $value)
    {
        $this->SMTPserver = $value;
    }

    /**
     * Set SMTP server port
     *
     * @param int $value
     */
    public function setSmtpPort(int $value)
    {
        $this->SMTPport = $value;
    }

    /**
     * Set SMTP username
     *
     * @param string $value
     */
    public function setSmtpUsername(string $value)
    {
        $this->SMTPusername = $value;
    }

    /**
     * Set SMTP password
     *
     * @param string $value
     */
    public function setSmtpPassword(string $value)
    {
        $this->SMTPpassword = $value;
    }

    /**
     * Set SMTP force secure connection
     *
     * @param bool $value
     */
    public function setSmtpSecure(bool $value)
    {
        $this->forceSMTPsecure = $value;
    }

    /**
     * Set FromEmail
     *
     * @param string $value
     */
    public function setFromEmail(string $value)
    {
        $this->fromEmail = $value;
    }

    /**
     * Set FromName
     *
     * @param string $value
     */
    public function setFromName(string $value)
    {
        $this->fromName = $value;
    }

    /**
     * Set message subject
     *
     * @param string $value
     */
    public function setSubject(string $value)
    {
        $this->subject = $value;
    }

    /**
     * Set recipient name
     *
     * @param string $value
     */
    public function setToName(string $value)
    {
        $this->toName = $value;
    }

    /**
     * Set recipient Email address
     *
     * @param string $value
     */
    public function setToEmail(string $value)
    {
        $this->toEmail = $value;
    }

    /**
     * Set the message plain text
     *
     * @param string $value [description]
     */
    public function setTextPlain(string $value)
    {
        $this->textHtml = $value;
    }

    /**
     * Set the message html text
     *
     * @param string $value
     */
    public function setTextHtml(string $value)
    {
        $this->textHtml = $value;
    }

    /**
     * Enable debugging mode
     */
    public function setDebugEnable()
    {
        $this->debugEnable = true;
    }

    /**
     * Add inline imege
     *
     * @param  string $file Path to image file
     * @return string       Image ID
     */
    public function addInlineImage(string $file)
    {
        if (empty($this->hostName))
        {
            throw new Exception('[hostName] property must be defined first before it can be used in this method');
        }

        if (! file_exists($file))
        {
            throw new Exception('Could not find file "' . $file . '"');
        }

        $hash = substr(sha1($file . microtime(true)), 0, 12);
        $fileExt  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $fileName = $hash . '.' . $fileExt;
        $fileData = base64_encode(file_get_contents($file));
        $fileId   = $hash . '@' . $this->hostName;

        $this->inlineImages[$this->inlineImagesKey]['original-filename'] = $fileName;
        $this->inlineImages[$this->inlineImagesKey]['file-extension']    = $fileExt;
        $this->inlineImages[$this->inlineImagesKey]['base64-data']       = $fileData;
        $this->inlineImages[$this->inlineImagesKey]['content-id']        = $fileId;

        $this->inlineImagesKey++;

        return $fileId;
    }

    /**
     * Add attachment file
     *
     * @param   string  $file  Path to file
     */
    public function addAttachment(string $file)
    {
        if (! file_exists($file))
        {
            throw new Exception('Could not find file "' . $file . '"');
        }

        $fileName = rawurlencode(pathinfo($file, PATHINFO_BASENAME));
        $fileExt  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $fileData = base64_encode(file_get_contents($file));
        $fileSize = filesize($file);

        $this->attachments[$this->attachmentsKey]['original-filename'] = $fileName;
        $this->attachments[$this->attachmentsKey]['file-extension']    = $fileExt;
        $this->attachments[$this->attachmentsKey]['base64-data']       = $fileData;
        $this->attachments[$this->attachmentsKey]['size']              = $fileSize;

        $this->attachmentsKey++;
    }

    /**
     * Clear all attachments
     */
    public function clearAttachments()
    {
        if (! empty($this->attachments))
        {
            $this->attachments = [];
            $this->attachmentsKey = 0;
        }
    }

    /**
     * Send the message
     *
     * @return boolean
     */
    public function sendMessage()
    {
        $this->prepareMessageData();
        $this->composeMessage();

        if ($this->mailMethod == 'smtp' && ! $this->isConnectionOpen())
        {
            if (! $this->SMTPconnectionOpen())
            {
                $this->debug('Failed to establish SMTP connection.');
                return false;
            }
        }

        switch ($this->mailMethod)
        {
            case 'mail':
                $to      = preg_replace('/^To:\s/', '', $this->encodeNameHeader('To', $this->toName, $this->toEmail));
                $subject = preg_replace('/^Subject:\s/', '', $this->encodeHeader('Subject', $this->subject));
                $headers = implode(self::CRLF, $this->mimeHeaders) . self::CRLF;
                if (mail($to, $subject, $this->mimeBody, $headers, '-f' . $this->fromEmail) !== false)
                {
                    return $this->messageId;
                }
                return false;
            case 'smtp':
                $this->mailString = implode(self::CRLF, $this->mimeHeaders) . self::CRLF . self::CRLF . $this->mimeBody;
                if ($this->isCapable('SIZE'))
                {
                    $len = strlen($this->mailString);
                    if ($len > $this->SMTPextensions['SIZE'])
                    {
                        $this->debug('Message size ' . $len . ' exceeds server\'s limit of ' . $this->SMTPextensions['SIZE'] . ' bytes.');
                        $this->CounterFail++;
                        return false;
                    }
                }
                if ($this->SMTPmail())
                {
                    $this->counterSuccess++;
                    return $this->messageId;
                }
                return false;
            default:
                throw new Exception('Illegal value of property mailMethod');
          }
    }

    /**
     * Destructor
     *
     * @return  void
     */
    public function __destruct()
    {
        if ($this->mailMethod == 'smtp' && $this->isConnectionOpen())
        {
            $this->SMTPconnectionClose();
        }
    }

    /**
     * Get Hostname
     *
     * @return string
     */
    private function getHostName()
    {
        if (isset($_SERVER['SERVER_NAME']))
        {
            return $_SERVER['SERVER_NAME'];
        }

        return isset($_SERVER['SERVER_ADDR']) ? '[' . $_SERVER['SERVER_ADDR'] . ']' : '[127.0.0.1]';
    }

    /**
     * Prepare message data
     */
    private function prepareMessageData()
    {
        $pregPattern = '/(?:\n|\r|\t|%0A|%0D|%08|%09)+/i';

        $this->toEmail   = filter_var($this->toEmail, FILTER_SANITIZE_EMAIL);
        $this->fromEmail = filter_var($this->fromEmail, FILTER_SANITIZE_EMAIL);
        $this->toName    = preg_replace($pregPattern, '', $this->toName);
        $this->fromName  = preg_replace($pregPattern, '', $this->fromName);
        $this->subject   = preg_replace($pregPattern, '', $this->subject);
    }

    /**
     * Compose email message
     */
    private function composeMessage()
    {
        $this->boundStr = false;

        if ($this->mailMethod != 'mail')
        {
            $this->mimeHeaders[] = $this->encodeNameHeader('To', $this->toName, $this->toEmail);
            $this->mimeHeaders[] = $this->encodeHeader('Subject', $this->subject);
        }

        $this->mimeHeaders[] = 'Date: ' . date('D, j M Y H:i:s O (T)');
        $this->mimeHeaders[] = $this->encodeNameHeader('From', $this->fromName, $this->fromEmail);
        $this->mimeHeaders[] = $this->createMessageId();

        if (! empty($this->customHeaders) && is_array($this->customHeaders))
        {
            foreach ($this->customHeaders as $key => $val)
            {
                $key = preg_replace('/(?:\n|\r|\t|%0A|%0D|%08|%09)+/i', '', $key);
                $val = preg_replace('/(?:\n|\r|\t|%0A|%0D|%08|%09)+/i', '', $val);
                $this->mimeHeaders[] = $this->encodeHeader($key, $val);
            }
        }

        $this->mimeHeaders[] = $this->foldLine('X-Mailer: UniMailer');
        $this->mimeHeaders[] = 'MIME-Version: 1.0';

        $multiTypes = [];
        $i = -1;

        if (! empty($this->attachments) && count($this->attachments) > 1
            && empty($this->textPlain) && empty($this->textHtml))
        {
            $i++;
            $multiTypes[$i] = 'multipart/mixed';
        }
        elseif (! empty($this->attachments)
            && (! empty($this->textPlain) || ! empty($this->textHtml)))
        {
            $i++;
            $multiTypes[$i] = 'multipart/mixed';

            if (! empty($this->textPlain))
            {
                $i++;
                $multiTypes[$i] = 'multipart/alternative';

                if (! empty($this->textHtml))
                {
                    $i++;
                    $multiTypes[$i] = 'multipart/related';
                }
            }
            elseif (! empty($this->textHtml))
            {
                if (! empty($this->inlineImages) || ! empty($this->attachments))
                {
                    $i++;
                    $multiTypes[$i] = 'multipart/related';
                }
                else
                {
                    $i++;
                    $multiTypes[$i] = 'multipart/alternative';
                }
            }
        }
        else
        {
            if (! empty($this->textPlain) && ! empty($this->textHtml))
            {
                $i++;
                $multiTypes[$i] = 'multipart/alternative';
            }
            elseif (! empty($this->textHtml) && ! empty($this->inlineImages))
            {
                $i++;
                $multiTypes[$i] = 'multipart/related';
            }
        }

        if ($i > -1)
        {
            $go  = true;
            $k   = 0;
            $dir = 'up';

            while ($go)
            {
                if ($k > $i)
                {
                    $k--;
                    $dir = 'down';
                }

                if ($dir == 'up')
                {
                    if ($k == 0)
                    {
                        $this->mimeHeaders[] = 'Content-Type: ' . $multiTypes[$k] . ';';
                        $this->mimeHeaders[] = "\tboundary=\"" . $this->getBoundary($multiTypes[$k]) . '"';

                        if (! empty($this->textPlain) || ! empty($this->textHtml))
                        {
                            $this->mimeBody .= '--' . $this->getBoundary($multiTypes[$k]) . self::CRLF;
                        }
                    }
                    else
                    {
                        $this->mimeBody .= 'Content-Type: ' . $multiTypes[$k] . ';' . self::CRLF;
                        $this->mimeBody .= "\tboundary=\"" . $this->getBoundary($multiTypes[$k]) . '"' . self::CRLF;
                        $this->mimeBody .= self::CRLF;
                        $this->mimeBody .= '--' . $this->getBoundary($multiTypes[$k]) . self::CRLF;
                    }

                    if ($multiTypes[$k] == 'multipart/alternative')
                    {
                        if (! empty($this->textContentLanguage))
                        {
                            $this->mimeBody .= 'Content-Language: ' . $this->textContentLanguage . self::CRLF;
                        }

                        $this->mimeBody .= 'Content-type: text/plain; charset=' . self::CHARSET . self::CRLF;
                        $this->mimeBody .= 'Content-Transfer-Encoding: ' . $this->textEncoding . self::CRLF;
                        $this->mimeBody .= self::CRLF;
                        $this->mimeBody .= $this->encodeBody(trim($this->textPlain)) . self::CRLF;
                        $this->mimeBody .= '--' . $this->getBoundary($multiTypes[$k]) . self::CRLF;
                    }
                    elseif ($multiTypes[$k] == 'multipart/related')
                    {
                        if (! empty($this->textContentLanguage))
                        {
                            $this->mimeBody .= 'Content-Language: ' . $this->textContentLanguage . self::CRLF;
                        }

                        $this->mimeBody .= 'Content-type: text/html; charset=' . self::CHARSET . self::CRLF;
                        $this->mimeBody .= 'Content-Transfer-Encoding: ' . $this->textEncoding . self::CRLF;
                        $this->mimeBody .= self::CRLF;
                        $this->mimeBody .= $this->encodeBody(trim($this->textHtml)) . self::CRLF;

                        if (! empty($this->inlineImages))
                        {
                            $this->mimeBody .= $this->createInlineImagesParts($multiTypes[$k]);
                        }
                    }
                    $k++;
                }
                else
                {
                    if ($multiTypes[$k] == 'multipart/mixed')
                    {
                        $this->mimeBody .= $this->createAttachmentsParts($multiTypes[$k]);
                    }

                    $this->mimeBody .= '--' . $this->getBoundary($multiTypes[$k]) . '--' . self::CRLF . self::CRLF;
                    $k--;
                }

                if ($k == -1)
                {
                    $go = false;
                }
            }

            $this->mimeBody = rtrim($this->mimeBody) . self::CRLF;
        }
        else
        {
            if (! empty($this->textPlain))
            {
                if (! empty($this->textContentLanguage))
                {
                    $this->mimeHeaders[] = 'Content-Language: ' . $this->textContentLanguage;
                }

                $this->mimeHeaders[] = 'Content-type: text/plain; charset=' . self::CHARSET;
                $this->mimeHeaders[] = 'Content-Transfer-Encoding: ' . $this->textEncoding;
                $this->mimeBody      = $this->encodeBody(trim($this->textPlain));
            }
            elseif (! empty($this->textHtml))
            {
                if (! empty($this->textContentLanguage))
                {
                    $this->mimeHeaders[] = 'Content-Language: ' . $this->textContentLanguage;
                }

                $this->mimeHeaders[] = 'Content-type: text/html; charset=' . self::CHARSET;
                $this->mimeHeaders[] = 'Content-Transfer-Encoding: ' . $this->textEncoding;
                $this->mimeBody      = $this->encodeBody(trim($this->textHtml));
            }
            elseif (! empty($this->attachments))
            {
                foreach ($this->attachments as $atk => $atv)
                {
                    $this->mimeHeaders[] = 'Content-Type: ' . MIMEtypes::get($atv['file-extension']) . ';';
                    $this->mimeHeaders[] = "\tname=\"" . $atv['original-filename'] . '"';
                    $this->mimeHeaders[] = 'Content-Transfer-Encoding: base64';
                    $this->mimeHeaders[] = 'Content-Disposition: attachment;';
                    $this->mimeHeaders[] = "\tfilename=\"" . $atv['original-filename'] . '";';
                    $this->mimeHeaders[] = "\tsize=" . $atv['size'];
                    $this->mimeBody      = chunk_split($atv['base64-data'], self::WRAP_LEN, self::CRLF);
                }
            }
            else
            {
                throw new Exception('There\'s no content');
            }
        }
    }

    /**
     * Check if connection is open
     *
     * @return boolean
     */
    private function isConnectionOpen()
    {
        if (empty($this->SMTPsocket))
        {
            return false;
        }

        if (is_resource($this->SMTPsocket))
        {
            $status = stream_get_meta_data($this->SMTPsocket);

            if ($status['eof'])
            {
                $this->SMTPconnectionClose();
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * Open the SMTP connection
     *
     * @return  boolean
     */
    private function SMTPconnectionOpen()
    {
        $this->epochConnectionOpened = microtime(true);
        $this->counterSuccess = 0;

        $this->debug('START NEW SMTP CONNECTION');

        $context = stream_context_create();

        if ($this->forceSMTPsecure)
        {
            if (! empty($this->CAfile))
            {
                $this->debug('Using CA certificate file ' . $this->CAfile);

                stream_context_set_option($context, 'ssl', 'cafile', $this->CAfile);
                stream_context_set_option($context, 'ssl', 'verify_host', true);
                stream_context_set_option($context, 'ssl', 'verify_peer', true);
                stream_context_set_option($context, 'ssl', 'verify_peer_name', true);
                stream_context_set_option($context, 'ssl', 'allow_self_signed', false);
              }
              else
              {
                stream_context_set_option($context, 'ssl', 'verify_host', false);
                stream_context_set_option($context, 'ssl', 'verify_peer', false);
                stream_context_set_option($context, 'ssl', 'verify_peer_name', false);
                stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
              }
        }

        $this->SMTPsocket = stream_socket_client($this->SMTPserver . ':' . $this->SMTPport, $errno, $errstr, $this->SMTPtimeout, STREAM_CLIENT_CONNECT, $context);

        $this->debug('Connecting to server ' . strtoupper($this->SMTPserver) . ' on port ' . $this->SMTPport . ' ...');

        if (! is_resource($this->SMTPsocket))
        {
            $this->debug('ERROR: Failed to connect: ' . $errstr . ' (' . $errno . ')');
            return false;
        }

        $greeting = $this->getLines();

        $this->debug('<<< ' . $greeting);

        if (substr($greeting, 0, 3) != '220')
        {
            return false;
        }

        if (! $this->sendCommand('EHLO ' . $this->hostName, 250))
        {
            return false;
        }

        $this->updateExtensionsList();

        if ($this->isCapable('STARTTLS'))
        {
            if (! $this->forceSMTPsecure && (
                ! empty($this->CAfile) || ! empty($this->SMTPusername) || ! empty($this->SMTPpassword)))
            {
                $this->debug('ERROR: SMTPsecure is disabled.');
                return false;
            }

            if (! $this->startTLS())
            {
                $this->debug('ERROR: Server ' . strtoupper($this->SMTPserver) . ' gave unexpected reply to STARTTLS command.');
                return false;
            }
        }
        elseif ($this->forceSMTPsecure)
        {
            $this->debug('ERROR: Server ' . strtoupper($this->SMTPserver) . ' does not support STARTTLS.');
            return false;
        }

        if ($this->isCapable('AUTH'))
        {
            if (! $this->authenticate())
            {
                $this->debug('ERROR: Authentication failed.');
                return false;
            }
        }

        return true;
    }

    /**
     * Close SMTP connection
     */
    private function SMTPconnectionClose()
    {
        $this->debug('---------------------------------------------------------');
        $this->sendCommand('QUIT', 221);
        $this->debug('---------------------------------------------------------');
        $this->debug('MESSAGES-SENT: ' . $this->counterSuccess . '; CONNECTION-TIME: ' . $this->benchmark($this->epochConnectionOpened));
        $this->debug('---------------------------------------------------------');

        fclose($this->SMTPsocket);

        $this->SMTPsocket = null;
        $this->epochConnectionOpened = null;
    }

    /**
     * Send SMTP commands
     *
     * @return  boolean
     */
    private function SMTPmail()
    {
        $this->debug('---------------------------------------------------------');

        if (! $this->sendCommand('MAIL FROM:<' . $this->fromEmail . '>', 250))
        {
            return false;
        }

        if (! $this->sendCommand('RCPT TO:<' . $this->toEmail . '>', [250, 251]))
        {
            return false;
        }

        if (! $this->sendCommand('DATA', 354))
        {
            return false;
        }

        if (! $this->sendCommand($this->mailString . self::CRLF . '.', 250))
        {
            return false;
        }

        return true;
    }

    /**
     * Check SMTP capablity
     *
     * @param  string  $ext Extension name
     * @return boolean
     */
    private function isCapable(string $ext)
    {
        if (empty($this->SMTPextensions))
        {
            return false;
        }

        return array_key_exists($ext, $this->SMTPextensions);
    }

    /**
     * SMTP authentication
     *
     * @return boolean
     */
    private function authenticate()
    {
        if (empty($this->SMTPusername) || empty($this->SMTPpassword))
        {
            $this->debug('ERROR: Authentication requires non-empty username and password.');
            return false;
        }

        $mechanismes = ['PLAIN', 'LOGIN', 'CRAM-MD5'];

        foreach ($mechanismes as $mechanism)
        {
            if (in_array($mechanism, $this->SMTPextensions['AUTH']))
            {
                break;
            }

            $this->debug('ERROR: No matching authentication mechanism.');
            return false;
        }

        switch ($mechanism)
        {
            case 'PLAIN':
                if (! $this->sendCommand('AUTH PLAIN', 334))
                {
                    return false;
                }
                if (! $this->sendCommand(base64_encode("\0" . $this->SMTPusername . "\0" . $this->SMTPpassword), 235))
                {
                    return false;
                }
                break;
            case 'LOGIN':
                if (! $this->sendCommand('AUTH LOGIN', 334))
                {
                    return false;
                }
                if (! $this->sendCommand(base64_encode($this->SMTPusername), 334))
                {
                    return false;
                }
                if (! $this->sendCommand(base64_encode($this->SMTPpassword), 235))
                {
                    return false;
                }
                break;
            case 'CRAM-MD5':
                if (! $this->sendCommand('AUTH CRAM-MD5', 334))
                {
                    return false;
                }
                $challenge = base64_decode(substr($this->lastReply, 4));
                $response  = $this->SMTPusername . ' ' . hash_hmac('md5', $challenge, $this->SMTPpassword);
                if (!$this->sendCommand('Username', base64_encode($response), 235))
                {
                    return false;
                }
                break;
            default:
                $this->debug('ERROR: Unsupported authentication mechanism: ' . $mechanism);
                return false;
        }

        return true;
    }

    /**
     * Start TLS
     *
     * @return boolean
     */
    private function startTLS()
    {
        if (! $this->sendCommand('STARTTLS', 220))
        {
            return false;
        }

        $method = STREAM_CRYPTO_METHOD_TLS_CLIENT;

        if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT'))
        {
            $method |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            $method |= STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
        }

        $this->debug('Selected CRYPTO METHOD ' . $method);

        stream_socket_enable_crypto($this->SMTPsocket, true, $method);

        if (! $this->sendCommand('EHLO ' . $this->hostName, 250))
        {
            return false;
        }

        $this->updateExtensionsList();

        return true;
    }

    /**
     * Update SMTP extensions list
     */
    private function updateExtensionsList()
    {
        $ext = explode(self::CRLF, trim($this->lastReply));

        foreach ($ext as $val)
        {
            if (preg_match('/^\d{3}(\ |-)([A-Z0-9]{2,66})(\ ([A-Z0-9\ -]{1,128}))?$/', $val, $match))
            {
                if (empty($this->SMTPextensions[$match[2]]))
                {
                    if (! empty($match[2]) && $match[2] == 'SIZE' && ! empty($match[4]))
                    {
                        $this->SMTPextensions[$match[2]] = $match[4];
                    }
                    elseif (! empty($match[2]) && $match[2] == 'AUTH' && ! empty($match[4]))
                    {
                        $this->SMTPextensions[$match[2]] = explode(' ', $match[4]);
                    }
                    else
                    {
                        $this->SMTPextensions[$match[2]] = true;
                    }
                }
            }
        }
    }

    /**
     * Send SMTP command
     *
     * @param  string         $commandstring  Command name
     * @param  integer|array  $expect         Command code|codes
     * @return boolean
     */
    private function sendCommand(string $commandstring, $expect)
    {
        $this->debug('>>> ' . $commandstring);

        fwrite($this->SMTPsocket, $commandstring . self::CRLF);

        $this->lastReply = $this->getLines();

        $this->debug('<<< ' . $this->lastReply);

        $code = substr($this->lastReply, 0, 3);

        if (! in_array($code, (array) $expect))
        {
            return false;
        }

        return true;
    }

    /**
     * Get SMTP server response line
     *
     * @return string
     */
    private function getLines()
    {
        if (! is_resource($this->SMTPsocket))
        {
           return '';
        }

        $data = '';
        $endTime = 0;

        stream_set_timeout($this->SMTPsocket, $this->SMTPtimeout);

        if ($this->SMTPtimeLimit > 0)
        {
            $endTime = time() + $this->SMTPtimeLimit;
        }

        while (is_resource($this->SMTPsocket) && ! feof($this->SMTPsocket))
        {
            $str = @fgets($this->SMTPsocket, 515);
            $data .= $str;

            if (isset($str[3]) && $str[3] == ' ')
            {
                break;
            }

            $info = stream_get_meta_data($this->SMTPsocket);

            if ($info['timed_out'])
            {
                break;
            }

            if ($endTime && time() > $endTime)
            {
                break;
            }
        }

        return $data;
    }

    /**
     * Inline images parts
     *
     * @param  string $boundaryKey Boundary key
     * @return string
     */
    private function createInlineImagesParts(string $boundaryKey)
    {
        $str = '';

        foreach ($this->inlineImages as $key => $val)
        {
            $str .= '--' . $this->getBoundary($boundaryKey) . self::CRLF;
            $str .= 'Content-ID: <' . $val['content-id'] . '>' . self::CRLF;
            $str .= 'Content-Type: ' . MIMEtypes::get($val['file-extension']) . '; name="' . $val['original-filename'] . '"' . self::CRLF;
            $str .= 'Content-Transfer-Encoding: base64' . self::CRLF;
            $str .= 'Content-Disposition: inline; filename="' . $val['original-filename'] . '"' . self::CRLF;
            $str .= self::CRLF;
            $str .= chunk_split($val['base64-data'], self::WRAP_LEN, self::CRLF) . self::CRLF;
        }

        return $str;
    }

    /**
     * Attachments parts
     *
     * @param  string $boundaryKey Boundary key
     * @return string
     */
    private function createAttachmentsParts(string $boundaryKey)
    {
        $str = '';

        foreach ($this->attachments as $key => $val)
        {
            $str .= '--' . $this->getBoundary($boundaryKey) . self::CRLF;
            $str .= 'Content-Type: ' . MIMEtypes::get($val['file-extension']) . ';' . self::CRLF;
            $str .= "\tname=\"" . $val['original-filename'] . '"' . self::CRLF;
            $str .= 'Content-Transfer-Encoding: base64' . self::CRLF;
            $str .= 'Content-Disposition: attachment;' . self::CRLF;
            $str .= "\tfilename=\"" . $val['original-filename'] . '";' . self::CRLF;
            $str .= "\tsize=" . $val['size'] . self::CRLF;
            $str .= self::CRLF;
            $str .= chunk_split($val['base64-data'], self::WRAP_LEN, self::CRLF) . self::CRLF;
        }

        return $str;
    }

    /**
     * Get boundary
     *
     * @param  string $key Boundary key
     * @return string
     */
    private function getBoundary(string $key)
    {
        if (empty($this->boundStr))
        {
            $this->boundStr = strtoupper(sha1(microtime(true)));
        }

        if (empty($this->boundary[$key]))
        {
            $this->boundary[$key] = '__' . strtoupper(substr(sha1($key . microtime(true)), 0, 8)) . ':' . $this->boundStr . '__';
        }

        return $this->boundary[$key];
    }

    /**
     * Get message ID for header
     *
     * @return string Message ID
     */
    private function createMessageId()
    {
        if (! $this->hostName || ! is_string($this->hostName))
        {
            throw new Exception('Undefined or invalid property hostName');
        }

        $str = substr(sha1(microtime(true)), 0, 30);
        $chunks = str_split($str, 10);

        $prefix = '';

        foreach ($chunks as $chunk)
        {
            $prefix .= $chunk . '-';
        }

        $prefix = trim($prefix, '-');

        $this->messageId = $prefix . '@' . $this->hostName;

        return 'Message-ID: <' . $this->messageId . '>';
    }

    /**
     * Format display name for message header
     *
     * @param  string $name    Display name
     * @param  string $comment Optional
     * @return string
     */
    public function formatDisplayName(string $name, string $comment = '')
    {
        if (preg_match('~[,;:\(\)\[\]\.\\<>@"]~', $name))
        {
            $name = preg_replace('/"/', '\"', $name);

            if (! empty($comment))
            {
                return '"' . $name . '" (' . $comment . ')';
            }

            return '"' . $name . '"';
        }

        if (! empty($comment))
        {
            return '"' . $name . '" (' . $comment . ')';
        }

        return $name;
    }

    /**
     * Encoding display name per RFC5322 format
     *
     * @param  string $hdr   Header of line
     * @param  string $name  Name string
     * @param  string $email Email string
     * @return string
     */
    private function encodeNameHeader(string $hdr, string $name, string $email)
    {
        $strName = $this->encodeHeader($hdr, $name, false);

        return $this->foldLine($strName . ' <' . $email . '>');
    }

    /**
     * Encoding the string of header
     *
     * @param  string       $hdr  Header of line
     * @param  string       $str  String of header
     * @param  bool|boolean $fold Fold the line
     * @return string
     */
    private function encodeHeader(string $hdr, string $str, bool $fold = true)
    {
        if ($fold === true)
        {
            return $this->foldLine($hdr . ': ' . $this->encodeString($str));
        }

        return $hdr . ': ' . $this->encodeString($str);
    }

    /**
     * Encoding substrings that are non ASCII
     *
     * @param  string $str Encoded string
     * @return string
     */
    private function encodeString(string $str)
    {
        $str = preg_replace('/\s+/', ' ', $str);

        if ($this->isMbStr($str))
        {
            $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
            $arr   = [];
            $key   = 0;

            foreach ($chars as $kc => $ch)
            {
                if ($kc == 0)
                {
                    $mb = $this->isMbStr($ch);
                    $arr[$key] = $ch;
                }
                else
                {
                    $mbPrev = $mb;

                    if ($ch != ' ')
                    {
                        $mb = $this->isMbStr($ch);
                    }
                    if ($mbPrev == $mb)
                    {
                        $arr[$key] .= $ch;
                    }
                    else
                    {
                        $key++;
                        $arr[$key] = $ch;
                    }
                }
            }

            $str = '';

            foreach ($arr as $segm)
            {
                if ($this->isMbStr($segm))
                {
                    $arr = $this->breakToSegments($segm);

                    foreach ($arr as $ak => $av)
                    {
                        $arr[$ak] = $this->encodeToRFC2047($av);
                    }

                    $str .= implode(' ', $arr);
                }
                else
                {
                    $str .= $segm;
                }
            }
        }

        return $str;
    }

    /**
     * Break string of multibyte characters into shorter segments
     *
     * @param   string $str String to convert
     * @return  array       Array of segments
     */
    private function breakToSegments(string $str)
    {
        $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        $arr = [];
        $i = 0;

        foreach ($chars as $item)
        {
            if (empty($arr[$i]))
            {
                $arr[$i] = $item;
            }
            else {
                $arr[$i] .= $item;
            }

            if (mb_strlen($arr[$i]) == self::MB_LEN_MAX)
            {
                $i++;
            }
        }

        return $arr;
    }

    /**
     * Encodes non-ASCII text for MIME message headers according to RFC 2047
     *
     * @param  string $str    String to encoding
     * @param  string $scheme Specifies the encoding method ('B' or 'Q')
     * @return string
     */
    private function encodeToRFC2047(string $str, string $scheme = 'b')
    {
        if (strtolower($scheme) == 'b')
        {
            return '=?' . self::CHARSET . '?B?' . base64_encode($str) . '?=';
        }

        if (strtolower($scheme) == 'q')
        {
            return '=?' . self::CHARSET . '?Q?' . str_replace([' ','?','_'], ['=20','=3F','=5F'], quoted_printable_encode($str)) . '?=';
        }
    }

    /**
     * Check if is multibyte string
     *
     * @param  string  $str String to checking
     * @return boolean
     */
    private function isMbStr(string $str)
    {
        return iconv_strlen($str, 'utf-8') < strlen($str);
    }

    /**
     * Folding of excessively long header lines
     *
     * @param   string $str String to folding
     * @return  mixed
     */
    private function foldLine(string $str)
    {
        if (! $this->isMbStr($str) && strlen($str) <= self::WRAP_LEN)
        {
            return $str;
        }

        $chunks = explode(self::CRLF, wordwrap($str, self::WRAP_LEN - 1, self::CRLF));
        $arr = [];

        foreach ($chunks as $item)
        {
            if (strlen($item) > self::WRAP_LEN - 1 && strpos($item, '?=') !== false)
            {
                $vals = explode('?=', $item);

                foreach ($vals as $val)
                {
                    if (! empty($val))
                    {
                        $arr[] = $val . '?=';
                    }
                }
            }
            else
            {
                $arr[] = $item;
            }
        }

        if (implode(' ', $arr) > self::LINE_LEN_MAX)
        {
            throw new Exception('Line length exceeds RFC5322 limit of ' . self::LINE_LEN_MAX);
        }

        return implode(self::CRLF . ' ', $arr);
    }

    /**
     * Encode message body text
     *
     * @param  string $str Text to encoding
     * @return string
     */
    private function encodeBody(string $str)
    {
        if ($this->textEncoding == 'quoted-printable')
        {
            return quoted_printable_encode($str) . self::CRLF;
        }

        return chunk_split(base64_encode($str), self::WRAP_LEN, self::CRLF);
    }

    /**
     * Debug
     *
     * @param  string $str Message string of debug
     * @return mixed
     */
    private function debug(string $str)
    {
        if (! $this->debugEnable)
        {
            return;
        }

        switch ($this->debug['method'])
        {
            case 'echo':
                echo htmlentities(trim($str)) . PHP_EOL;
                break;
            case 'log':
                $this->appendLog($str);
                break;
            default:
                throw new Exception('Illegal value debug method.');
        }
    }

    /**
     * Append the string into the log file
     *
     * @param  string $str String of log
     */
    private function appendLog(string $str)
    {
        $str = trim($str);

        if (strlen($str) > 1000)
        {
            $str = substr($str, 0, 1000) . ' ... [truncated]';
        }

        $filePath = $this->debug['directory'] . $this->debug['file_name'];
        $msgLine  = '[' . gmdate("Y-m-d H:i:s", time() + $this->debug['time_offset']) . '] ' . $str . PHP_EOL;

        file_put_contents($filePath, $msgLine, FILE_APPEND | LOCK_EX);
    }

    /**
     * Benchmark for measurement timing
     *
     * @param  float  $start  Start time
     * @return string
     */
    private function benchmark(float $start)
    {
        $dif = microtime(true) - $start;

        if ($dif >= 1)
        {
            return number_format($dif, 2, '.', ',') . ' sec';
        }

        $dif = $dif * 1000;

        if ($dif >= 1)
        {
            return number_format($dif, 2, '.', ',') . ' msec';
        }

        $dif = $dif * 1000;

        return number_format($dif, 2, '.', ',') . ' μsec';
    }

}
