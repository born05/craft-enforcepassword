<?php

namespace born05\enforcepassword\services;

use born05\enforcepassword\Plugin as EnforcePassword;

use Craft;
use craft\base\Component;
use craft\elements\User;

class Security extends Component
{
    /**
     * Validates the password.
     *
     * @param User $user
     * @param string $password
     * @return boolean
     */
    public function validatePassword(User $user, string $password)
    {
        $settings = EnforcePassword::$plugin->getSettings();

        if (mb_strlen($password) < $settings->passwordMinLength) {
            $user->addError('newPassword', Craft::t('enforce-password', "Password should be at least {length} characters.", [ 'length' => $settings->passwordMinLength ]));
        }
        if (mb_strlen($password) > $settings->passwordMaxLength) {
            $user->addError('newPassword', Craft::t('enforce-password', "Password should be less than {length} characters.", [ 'length' => $settings->passwordMaxLength ]));
        }
        if ($settings->enforceUppercase && preg_match('/[A-Z]/', $password) !== 1) {
            $user->addError('newPassword', Craft::t('enforce-password', "Password should contain at least 1 uppercase character."));
        }
        if ($settings->enforceLowercase && preg_match('/[a-z]/', $password) !== 1) {
            $user->addError('newPassword', Craft::t('enforce-password', "Password should contain at least 1 lowercase character."));
        }
        if ($settings->enforceDigit && preg_match('/\d/', $password) !== 1) {
            $user->addError('newPassword', Craft::t('enforce-password', "Password should contain at least 1 digit."));
        }
        if ($settings->enforceSymbol && preg_match('/[^a-zA-Z\d]/', $password) !== 1) {
            $user->addError('newPassword', Craft::t('enforce-password', "Password should contain at least 1 symbol."));
        }

        if ($user->email === $password || $user->username === $password) {
            $user->addError('newPassword', Craft::t('enforce-password', "Password can't be the same as your username or email."));
        }

        if (EnforcePassword::$plugin->history->isPasswordUsed($user, $password)) {
            $user->addError('newPassword', Craft::t('enforce-password', "Please choose a password you didn't use before."));
        }

        return !$user->hasErrors();
    }
}
