<?php

namespace Kanboard\Validator;

use SimpleValidator\Validator;
use SimpleValidator\Validators;

/**
 * Config Validator
 *
 * @package  Kanboard\Validator
 * @author   Frederic Guillot
 */
class ConfigValidator extends BaseValidator
{
    public function validate(array $values)
    {
        $v = new Validator($values, [
            new Validators\URL('application_url', t('This URL is invalid')),
            new Validators\InArray('application_language', array_keys($this->languageModel->getLanguages()), t('This language is invalid')),
            new Validators\Timezone('application_timezone', t('This timezone is invalid')),
            new Validators\InArray('application_date_format', $this->dateParser->getDateFormats(true), t('Date format invalid')),
            new Validators\InArray('application_time_format', $this->dateParser->getTimeFormats(), t('Time format invalid')),
            new Validators\Email('mail_sender_address', t('Email address invalid')),
            new Validators\InArray('mail_transport', array_keys($this->emailClient->getAvailableTransports()), t('Invalid Mail transport')),
            new Validators\InArray('default_color', array_keys($this->colorModel->getList()), t('Color invalid')),
            new Validators\Integer('board_highlight_period', t('This value must be an integer')),
            new Validators\Integer('board_public_refresh_interval', t('This value must be an integer')),
            new Validators\Integer('board_private_refresh_interval', t('This value must be an integer')),
            new Validators\GreaterThanOrEqual('board_highlight_period', t('This value must be greater or equal to %d', 0), 0),
            new Validators\GreaterThanOrEqual('board_public_refresh_interval', t('This value must be greater or equal to %d', 0), 0),
            new Validators\GreaterThanOrEqual('board_private_refresh_interval', t('This value must be greater or equal to %d', 0), 0),
        ]);

        return array(
            $v->execute(),
            $v->getErrors()
        );
    }
}
