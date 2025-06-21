<?php

namespace Kanboard\Model;

use Kanboard\Core\Base;

/**
 * Captcha model
 *
 * @package  Kanboard\Model
 *
 * {"<IP_ADDRESS>": {"failed_login": <COUNT>, "expiration_date": <TIMESTAMP>}}
 */
class CaptchaModel extends Base
{
    public function incrementFailedLogin($ipAddress)
    {
        $data = $this->getCaptchaData();
        if (!isset($data[$ipAddress])) {
            $data[$ipAddress] = ['failed_login' => 0, 'expiration_date' => 0];
        }

        $data[$ipAddress]['failed_login']++;
        if ($data[$ipAddress]['failed_login'] >= BRUTEFORCE_CAPTCHA) {
            $data[$ipAddress]['lock_expiration_date'] = time() + BRUTEFORCE_LOCKDOWN_DURATION;
        }

        $this->setCaptchaData($data);
    }

    public function resetFailedLogin($ipAddress)
    {
        $data = $this->getCaptchaData();
        if (isset($data[$ipAddress])) {
            unset($data[$ipAddress]);
            $this->setCaptchaData($data);
        }
    }

    public function isLocked($ipAddress)
    {
        $data = $this->getCaptchaData();
        if (isset($data[$ipAddress]) && isset($data[$ipAddress]['lock_expiration_date'])) {
            return $data[$ipAddress]['lock_expiration_date'] > time();
        }
        return false;
    }

    protected function getCaptchaData()
    {
        $rawData = $this->configModel->getOption('captcha_data', '{}');
        $data = json_decode($rawData, true);
        if (!is_array($data)) {
            $data = [];
        }
        return $data;
    }

    protected function setCaptchaData(array $data)
    {
        $this->configModel->save(['captcha_data' => json_encode($data)]);
    }
}
