<?php

namespace born05\enforcepassword\models;

use craft\base\Model;

class Settings extends Model
{
    public $passwordMinLength = 16;
    public $passwordMaxLength = 255;
    public $passwordHistoryLimit = 5; // Number of passwords kept in history, set to 0 to disable this feature
    public $passwordMaxLifetime = 90; // Number of days a password can be used
    public $enforceUppercase = true; // Min 1 uppercase letter 
    public $enforceLowercase = true; // Min 1 lowercase letter
    public $enforceDigit = true; // Min 1 digit
    public $enforceSymbol = true; // Min 1 symbol
}
