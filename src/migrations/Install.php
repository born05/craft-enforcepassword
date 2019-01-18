<?php

namespace born05\enforcepassword\migrations;

use born05\enforcepassword\records\Password as PasswordRecord;

use craft\db\Migration;
use craft\db\Query;
use craft\elements\User as UserElement;
use craft\records\User as UserRecord;
use craft\helpers\MigrationHelper;

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

        $userRecords = UserRecord::find()->all();
        foreach ($userRecords as $userRecord) {
            $passwordRecord = new PasswordRecord();
            $passwordRecord->userId = $userRecord->id;
            $passwordRecord->password = $userRecord->password;
            $passwordRecord->save();
        }
    }

    public function safeDown()
    {
        $this->dropTableIfExists(Password::tableName());

        return true;
    }
}