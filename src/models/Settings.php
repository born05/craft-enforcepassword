<?php

namespace born05\enforcepassword\models;

use craft\base\Model;

class Settings extends Model
{
    public $passwordMinLength = 16;
    public $passwordMaxLength = 255;
    public $passwordHistoryLimit = 5; // Number of passwords kept in history
    public $passwordMaxLifetime = 90; // Number of days a password can be used
}
