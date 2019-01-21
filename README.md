# Craft Enforce Password plugin

Incrementally enforces a new and secure password not matching the last 5 passwords.

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
    'passwordHistoryLimit' => 5,
];
```

See [license](https://github.com/born05/craft-enforcepassword/blob/master/LICENSE.md)
