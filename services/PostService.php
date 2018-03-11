<?php

namespace Qe;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Swift_Image;

/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 19.06.17
 * Time: 0:42
 */
class PostService implements ServiceProviderInterface
{

    /** @var Container $app **/
    private $app;

    public function post($enteredData)
    {

        $fields = [];

        foreach ($this->app['dataService']->getDocuments('form') as $document) {
            $key = $document['key'];
            if (array_key_exists($key, $enteredData)) {
                $fields[$document['key']] = [
                    'title' => $document['title'],
                    'value' => $enteredData[$key]
                ];
            }

        }

        $this->sendEmailToAdmin([
            'subject' => "Отправлена форма с сайта",
            'preheader' => "Отправлена форма с сайта",
            'data' => $fields
        ]);

        return $this->app['pageService']->render($this->app['settings']['form']['successfulSentPage']);

    }

    public function sendEmail($contentParameters, $email)
    {

        $message = new \Swift_Message();

        $embedImage = $message->embed(Swift_Image::fromPath(INDEX_PATH . $this->app['settings']['form']['logo']));

        $message->setSubject($contentParameters['subject'])
            ->setFrom($this->app['settings']['mail']['emailFrom'] ? $this->app['settings']['mail']['emailFrom'] : $this->app['settings']['mail']['username'])
            ->setTo($email)
            ->setBody(
                $this->app['twig']->render('/common/layouts/email.html.twig', array_merge(
                    $contentParameters, [
                        'logoSrc' => $embedImage,
                        'siteUrl' => $this->app['settings']['form']['siteUrl']
                    ])
                ),
                'text/html'
            )
        ;

        return $this->app['mailer']->send($message);

    }

    public function sendEmailToAdmin($contentParameters)
    {
        return $this->sendEmail($contentParameters, $this->app['settings']['form']['emailSendTo']);
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
        $this->app = $app;

        $app['postService'] = function () use ($app) {
            return $this;
        };
    }
}