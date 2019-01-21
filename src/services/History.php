<?php

namespace born05\enforcepassword\services;

use born05\enforcepassword\Plugin as EnforcePassword;
use born05\enforcepassword\jobs\RequirePasswordReset;
use born05\enforcepassword\records\Password as PasswordRecord;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\elements\User;
use craft\helpers\DateTimeHelper;

class History extends Component
{
    public function queuePasswordResets()
    {
        // Only trigger when lifetime is larger than 0
        if (EnforcePassword::$plugin->getSettings()->passwordMaxLifetime === 0) {
            return;
        }

        // Run this taks every day.
        $lastDate = Craft::$app->getCache()->get('enforcepassword_taskdate');
        $now = DateTimeHelper::currentUTCDateTime();

        if ($lastDate instanceof \DateTime) {
            $yesterday = $now->sub(new \DateInterval('P1D'));
            $yesterday->setTime(0, 0, 1);

            if ($lastDate < $yesterday) {
                Craft::$app->getCache()->set('enforcepassword_taskdate', $now);
                Craft::$app->getQueue()->push(new RequirePasswordReset());
            }
        } else {
            Craft::$app->getCache()->set('enforcepassword_taskdate', $now);
            Craft::$app->getQueue()->push(new RequirePasswordReset());
        }
    }

    /**
     * Determines if an password is used before.
     *
     * @param User $user
     * @param string $password
     * @return boolean
     */
    public function isPasswordUsed(User $user, string $newPassword)
    {
        // Only trigger when history is larger than 0
        $settings = EnforcePassword::$plugin->getSettings();
        if ($settings->passwordHistoryLimit === 0) {
            return false;
        }

        $oldPasswords = (new Query())
            ->select(['password'])
            ->from([PasswordRecord::tableName()])
            ->where(['userId' => $user->id])
            ->all();

        $securityService = Craft::$app->getSecurity();
        foreach ($oldPasswords as $oldPassword) {
            if ($securityService->validatePassword($newPassword, $oldPassword['password'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Updates a users password history
     *
     * @param User $user
     */
    public function updateHistoryByUser(User $user)
    {
        // Only trigger when history is larger than 0
        $settings = EnforcePassword::$plugin->getSettings();
        if ($settings->passwordHistoryLimit === 0) {
            return false;
        }

        // Determine if this password is new.
        $matchingPassword = PasswordRecord::find()
            ->where(['userId' => $user->id])
            ->andWhere(['password' => $user->password])
            ->one();

        // Password already in history.
        if (!empty($matchingPassword)) {
            return;
        }

        // Add new password
        $passwordRecord = new PasswordRecord();
        $passwordRecord->userId = $user->id;
        $passwordRecord->password = $user->password;
        $passwordRecord->save();

        $oldPasswordRecords = PasswordRecord::find()
            ->where(['userId' => $user->id])
            ->orderBy(['dateCreated' => SORT_DESC])
            ->all();

        // Delete passwords beyond the limit.
        $count = 0;
        foreach ($oldPasswordRecords as $oldPasswordRecord) {
            $count++;

            if ($count >= $settings->passwordHistoryLimit) {
                $oldPasswordRecord->delete();
            }
        }
    }
}
