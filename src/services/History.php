<?php

namespace born05\enforcepassword\services;

use born05\enforcepassword\records\Password as PasswordRecord;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\elements\User;

class History extends Component
{
    /**
     * Determines if an password is used before.
     *
     * @param User $user
     * @param string $password
     * @return boolean
     */
    public function isPasswordUsed(User $user, string $newPassword)
    {
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
        // Add new password
        $passwordRecord = new PasswordRecord();
        $passwordRecord->userId = $user->id;
        $passwordRecord->password = $user->password;
        $passwordRecord->save();

        $oldPasswordRecords = PasswordRecord::find()
            ->where(['userId' => $user->id])
            ->orderBy(['dateCreated' => 'desc'])
            ->all();

        // Delete passwords beyond the limit.
        $count = 0;
        foreach ($oldPasswordRecords as $oldPasswordRecord) {
            $count++;

            if ($count >= 5) {
                $oldPasswordRecord->delete();
            }
        }
    }
}

// $user->passwordResetRequired = true;