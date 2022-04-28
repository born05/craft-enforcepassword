<?php

namespace born05\enforcepassword\jobs;

use born05\enforcepassword\Plugin as EnforcePassword;

use Craft;
use craft\elements\User;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use craft\queue\BaseJob;

class RequirePasswordReset extends BaseJob
{
    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        $settings = EnforcePassword::$plugin->getSettings();

        // Only trigger when lifetime is larger than 0, false or null.
        if (!($settings->passwordMaxLifetime > 0)) {
            return;
        }

        $now = DateTimeHelper::currentUTCDateTime();
        $maxLifetime = $now->sub(new \DateInterval('P' . $settings->passwordMaxLifetime . 'D'));

        // Loop through users with old passwords.
        $users = User::find()
            ->where([
                '<', 'lastPasswordChangeDate', Db::prepareValueForDb($maxLifetime)
            ])
            ->andWhere(['passwordResetRequired' => false])
            ->anyStatus()
            ->all();

        $currentRow = 0;
        $totalRows = count($users);
        foreach ($users as $user) {
            $this->setProgress($queue, $currentRow++ / $totalRows);

            // Force password reset when password has passed lifetime.
            $user->passwordResetRequired = true;
            Craft::$app->getElements()->saveElement($user, false);
        }
    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return Craft::t('enforce-password', 'Require old password reset');
    }
}
