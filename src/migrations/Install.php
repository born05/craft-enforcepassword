<?php

namespace born05\enforcepassword\migrations;

use born05\enforcepassword\records\Password as PasswordRecord;

use craft\db\Migration;
use craft\elements\User;

class Install extends Migration
{
    public function safeUp()
    {
        $this->createTable(PasswordRecord::tableName(), [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'password' => $this->string(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->addForeignKey(null, PasswordRecord::tableName(), ['userId'], '{{%users}}', ['id'], 'CASCADE', null);

        $users = User::find()
            ->addSelect(['users.password'])
            ->status(null)
            ->all();
        foreach ($users as $user) {
            if (!empty($user->password)) {
                $passwordRecord = new PasswordRecord();
                $passwordRecord->userId = $user->id;
                $passwordRecord->password = $user->password;
                $passwordRecord->save();
            }
        }
    }

    public function safeDown()
    {
        $this->dropTableIfExists(PasswordRecord::tableName());

        return true;
    }
}