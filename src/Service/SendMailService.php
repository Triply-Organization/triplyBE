<?php

namespace App\Service;

use App\Event\EmailEvent;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SendMailService
{
    private ParameterBagInterface $params;

    /**
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $dispatcher;

    public function __construct(ParameterBagInterface $params, EventDispatcherInterface $dispatcher)
    {
        $this->params = $params;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @throws Exception
     */
    public function sendRegisterMail(string $email, string $subject): void
    {
        $mail = $this->mailConfig(
            $this->params->get('zoho.mail.host'),
            $this->params->get('zoho.mail.username'),
            $this->params->get('zoho.mail.password'),
            $this->params->get('zoho.mail.port'),
        );
        try {
            //Recipients
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = 'Welcome to our Website';
            $mail->send();

            $event = new EmailEvent($mail);
            $this->dispatcher->dispatch($event, CarEvent::SET);
        } catch (Exception $e) {
            throw new Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    /**
     * @throws Exception
     */
    private function mailConfig(string $host, string $username, string $password, int $port): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $port;
        $mail->setFrom($username, 'Triply');

        return $mail;
    }
}
