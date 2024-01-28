# Password Expiration Bundle

## About

Symfony bundle to enforce users to change their password every X days, and keeps track when the password was changed.

The bundle won't allow to user to navigate to any page unless they change their password.

It can be integrated with **EasyAdmin** bundle, but it's not a requirement.

This is meant for Symfony projects that use the MVC pattern, not for apis.

It doesn't support multiple login methods, so sites that have different login pages for admins and users are not supported.

## Install

`composer req robinbrackez/password-expiration-bundle`

Add the bundle to `config/bundles.php`

```
    RobinBrackez\PasswordExpirationBundle\PasswordExpirationBundle::class => ['all' => true],
```

### Add to your User class:

* `implements RobinBrackez\PasswordExpirationBundle\User\PasswordExpirableInterface`
* `use RobinBrackez\PasswordExpirationBundle\User\PasswordExpirableTrait`

**For instance:**

```
use RobinBrackez\PasswordExpirationBundle\User\PasswordExpirableInterface;
use RobinBrackez\PasswordExpirationBundle\User\PasswordExpirableTrait;

class User implements PasswordExpirableInterface {
    use PasswordExpirableTrait;
```

Create and run **migrations** to add the `password_changed_at` field to the User table.

There needs to be a property called 'password' inside your User entity, this is necessary for the listener to check on.

### Config:

* **changePasswordRouteName**: the name of the route to change your password
* **passwordMaxDaysOld**: the age in days when users need to change their password
* **easyadmin.changePasswordControllerName**: when working with EasyAdmin, pass the FQDN of the controller that handles changing passwords
* **easyadmin.changePasswordControllerActionName**: when working with EasyAdmin, pass the action name of the controller that handles changing passwords

**For instance:**

```
parameters:
    - changePasswordRouteName: 'app_change_password'
    - passwordMaxDaysOld: 30  # every month 
    - easyadmin.changePasswordControllerName: 'App\Controller\Admin\UserCrudController'
    - easyadmin.changePasswordControllerActionName: 'changePassword' # this is the method name of the changePasswordRouteName
```

If you don't have EasyAdmin, omit the easyadmin related parameters

## Only GET requests

It only forwards GET requests, not POSTS or so, to avoid that users get the change password screen when they submitted a form and would lose their changes.

## Versioning

Works with Symfony 5.4 and 6.x

Not yet a Symfony 7.x branch

Symfony 6.4 may get deprecate notices.