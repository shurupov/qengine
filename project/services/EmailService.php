<?php

namespace Qe;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Swift_Image;

/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 19.06.17
 * Time: 0:44
 */
class EmailService implements ServiceProviderInterface
{
    /** @var \Twig_Environment $twig **/
    private $twig;

    /** @var \Swift_Mailer $mailer **/
    private $mailer;

    public function sendEmail($contentParameters, $email)
    {

        $message = new \Swift_Message();

        $embedImage = $message->embed(Swift_Image::fromPath('/project/web/i/logo.png'));

        $message->setSubject($contentParameters['subject'])
            ->setFrom('bakalibriki.online@ya.ru')
            ->setTo($email)
            ->setBody(
                $this->twig->render('email/layout.html.twig', array_merge($contentParameters, ['logoSrc' => $embedImage])),
                'text/html'
            )
        ;

        return $this->mailer->send($message);

    }

    public function sendEmailToAdmin($contentParameters)
    {
        return $this->sendEmail($contentParameters, 'bolteg86@ya.ru');
    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        $this->twig = $app['twig'];
        $this->mailer = $app['mailer'];

        $app['emailService'] = function () use ($app) {
            return $this;
        };
    }
}