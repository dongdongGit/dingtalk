<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk;

use Overtrue\Http\Support\Collection;
use Pimple\Container;

/**
 * @property \EasyDingTalk\Auth\SsoClient $sso
 * @property \EasyDingTalk\Auth\OAuthClient $oauth
 * @property \EasyDingTalk\Chat\Client $chat
 * @property \EasyDingTalk\Role\Client $role
 * @property \EasyDingTalk\User\Client $user
 * @property \EasyDingTalk\Media\Client $media
 * @property \EasyDingTalk\H5app\Client $h5app
 * @property \EasyDingTalk\Health\Client $health
 * @property \EasyDingTalk\Report\Client $report
 * @property \EasyDingTalk\Checkin\Client $checkin
 * @property \EasyDingTalk\Contact\Client $contact
 * @property \EasyDingTalk\Process\Client $process
 * @property \EasyDingTalk\Calendar\Client $calendar
 * @property \EasyDingTalk\Callback\Client $callback
 * @property \EasyDingTalk\Microapp\Client $microapp
 * @property \EasyDingTalk\Schedule\Client $schedule
 * @property \EasyDingTalk\Blackboard\Client $blackboard
 * @property \EasyDingTalk\Attendance\Client $attendance
 * @property \EasyDingTalk\Department\Client $department
 * @property \EasyDingTalk\Conversation\Client $conversation
 * @property \EasyDingTalk\Smartwork\Client $smartwork
 * @property \EasyDingTalk\Kernel\Http\Client $client
 * @property \EasyDingTalk\Edu\Client $edu
 * @property \Monolog\Logger $logger
 * @property \EasyDingTalk\Kernel\Server $server
 * @property \Symfony\Component\HttpFoundation\Request $request
 * @property \EasyDingTalk\Kernel\Encryption\Encryptor $encryptor
 * @property \EasyDingTalk\Kernel\AccessToken $access_token
 */
class Application extends Container
{
    use Kernel\Concerns\InteractsWithCache;

    /**
     * 企业内部.
     */
    public const MODE_NORMAL = 0;

    /**
     * 第三方企业.
     */
    public const MODE_THIRD_PARTY_CORP = 1;

    /**
     * 第三方个人.
     */
    public const MODE_THIRD_PARTY_INDIVIDUALS = 2;

    /**
     * @var array
     */
    protected $providers = [
        Auth\ServiceProvider::class,
        Chat\ServiceProvider::class,
        Role\ServiceProvider::class,
        User\ServiceProvider::class,
        Media\ServiceProvider::class,
        H5app\ServiceProvider::class,
        Health\ServiceProvider::class,
        Report\ServiceProvider::class,
        Checkin\ServiceProvider::class,
        Contact\ServiceProvider::class,
        Process\ServiceProvider::class,
        Workflow\ServiceProvider::class,
        Calendar\ServiceProvider::class,
        Callback\ServiceProvider::class,
        Microapp\ServiceProvider::class,
        Schedule\ServiceProvider::class,
        Blackboard\ServiceProvider::class,
        Attendance\ServiceProvider::class,
        Department\ServiceProvider::class,
        Conversation\ServiceProvider::class,
        Smartwork\ServiceProvider::class,
        Edu\ServiceProvider::class,
        Kernel\Providers\ClientServiceProvider::class,
        Kernel\Providers\LoggerServiceProvider::class,
        Kernel\Providers\ServerServiceProvider::class,
        Kernel\Providers\RequestServiceProvider::class,
        Kernel\Providers\EncryptionServiceProvider::class,
        Kernel\Providers\AccessTokenServiceProvider::class,
    ];

    /**
     * Application constructor.
     *
     * @param array $config
     * @param array $values
     */
    public function __construct($config = [], array $values = [])
    {
        parent::__construct($values);

        $this['config'] = function () use ($config) {
            return new Collection($config);
        };

        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this[$name];
    }

    public function gateway($gateway = 'latest')
    {
        $this->client = $this->client->gateway($gateway);

        return $this;
    }

    public function version($version)
    {
        $this->client = $this->client->version($version);

        return $this;
    }

    public function authCorpId($auth_corp_id)
    {
        $this['config']->set('auth_corp_id', $auth_corp_id);

        return $this;
    }

    public function getSuiteTicket($suite_id = null)
    {
        $suite_id = $suite_id ?? $this['config']->get('suite_id');
        $ticket = $this['config']->get('ticket.' . $suite_id);
        if (empty($ticket)) {
            $ticket = $this->getCache()->get('ticket.' . $suite_id);
        }

        return $ticket;
    }
}
