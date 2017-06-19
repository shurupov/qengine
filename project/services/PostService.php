<?php

namespace Qe;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Created by PhpStorm.
 * User: shurupov
 * Date: 19.06.17
 * Time: 0:42
 */
class PostService implements ServiceProviderInterface
{

    /** @var EmailService $emailService **/
    private $emailService;

    public function test($arr)
    {
        var_dump($arr);
    }

    public function post($enteredData)
    {
        $data = [];

        foreach ($enteredData as $key=>$value) {
            if ($key == 'captions') continue;

            $data[$enteredData['captions'][$key]] = $value;
        }

        return $this->emailService->sendEmailToAdmin([
            'subject' => "Отправлена форма с сайта",
            'preheader' => "Отправлена форма с сайта",
            'data' => $data
        ]);

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
        $this->emailService = $app['emailService'];

        $app['postService'] = function () use ($app) {
            return $this;
        };
    }
}