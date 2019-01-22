<?php

namespace born05\enforcepassword\migrations;

use born05\enforcepassword\records\Password as PasswordRecord;

use craft\db\Migration;
use craft\db\Query;
use craft\elements\User;
use craft\helpers\MigrationHelper;

class m190122_095600_cleanup_history extends Migration
{
    public function safeUp()
    {
        $oldPasswordRecords = PasswordRecord::find()
            ->all();

        // Delete empty passwords.
        foreach ($oldPasswordRecords as $oldPasswordRecord) {
            if (empty($oldPasswordRecord->password)) {
                $oldPasswordRecord->delete();
            }
        }

        return true;
    }

    public function safeDown()
    {
        return true;
    }
}
