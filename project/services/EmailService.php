<?php

namespace Qe;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 19.06.17
 * Time: 0:44
 */
class EmailService implements ServiceProviderInterface
{
    public function sendEmail($contentParameters, $email)
    {

        $message = \Swift_Message::newInstance();

        $embedImage = $message->embed(Swift_Image::fromPath('/project/web/i/logo.png'));

        $message->setSubject($contentParameters['subject'])
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($email)
            ->setBody(
                $this->twig->render('email/layout.html.twig', array_merge($contentParameters, ['logoSrc' => $embedImage])),
                'text/html'
            )
        ;
        $this->mailer->send($message);

    }

    public function sendEmailToAdmin($contentParameters)
    {
        $this->sendEmail($contentParameters, 'bolteg86@ya.ru');
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
        $app['emailService'] = function () use ($app) {
            return $this;
        };
    }
}