# Craft Enforce Password plugin

Incrementally enforces a new and secure password not matching the last 5 passwords.
Stores password history in a database table.

Validates passwords by the following:
- Minimal length
- Maximum length
- At least 1 uppercase character
- At least 1 lowercase character
- At least 1 digit
- At least 1 symbol
- Can't match username or email
- Different from previous passwords

## Config

Create a `config/enforce-password.php` with the following contents:

```php
<?php

return [
    'passwordMinLength'    => 16,
    'passwordMaxLength'    => 255,
    'passwordHistoryLimit' => 5, // Number of passwords kept in history
    'passwordMaxLifetime'  => 90, // Number of days a password can be used
];
```
## License

Copyright © [Born05](https://www.born05.com/)

See [license](https://github.com/born05/craft-enforcepassword/blob/master/LICENSE.md)
