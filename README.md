<p align="center"><img height="500px" src="logo.svg"></p>

<p align="center">
<a href="https://github.com/Te7a-Houdini/laravel-applicant/releases"><img src="https://img.shields.io/github/release/Te7a-Houdini/laravel-applicant.svg?style=flat-square" alt="Releases"></a>
<a href="https://travis-ci.org/Te7a-Houdini/laravel-applicant"><img src="https://img.shields.io/travis/Te7a-Houdini/laravel-applicant/master.svg?style=flat-square" alt="Build Status"></a>
<a href="https://styleci.io/repos/201356561"><img src="https://styleci.io/repos/201356561/shield" alt="StyleCI"></a>
<a href="https://scrutinizer-ci.com/g/Te7a-Houdini/laravel-applicant/?branch=master"><img src="https://img.shields.io/scrutinizer/g/Te7a-Houdini/laravel-applicant.svg?style=flat-square" alt="Code Quality"></a>
<a href="https://github.com/Te7a-Houdini/laravel-applicant/blob/master/LICENSE.md"><img src="https://img.shields.io/github/license/Te7a-Houdini/laravel-applicant.svg?style=flat-square" alt="License"></a>
<a href="https://packagist.org/packages/te7a-houdini/laravel-applicant"><img src="https://img.shields.io/packagist/dt/Te7a-Houdini/laravel-applicant.svg?style=flat-square" alt="Downloads"></a>

</p>

Simple package to allow model applies and receives applications from other models.

for example it will allow you to do something like this:

``` php
//User model applies for Group model
$user->appliesFor($group);

//Group model process application from User model
$group->processApplicationFrom($user,'accepted');
```
* [Installation](#installation)
* [Usage](#usage)
  * [Using Applicant trait](#using-applicant-trait)
    * [Using model for creating applications](#using-model-for-creating-applications)
    * [Using array for creating applications](#using-array-for-creating-applications)
    * [Using null for creating applications](#using-null-for-creating-applications)
    * [Using hasAppliedFor()](#using-hasappliedfor)
    * [Using appliedApplicationsFor()](#using-appliedapplicationsfor)
    * [Using appliedApplications()](#using-appliedapplications)
    * [Using applicantCriteria Attribute](#using-applicantcriteria-attribute)
    * [Using setApplicantCriteria()](#using-setapplicantcriteria)
  * [Using ReceivesApplications trait](#using-receivesapplications-trait)
    * [Using model for processing applications](#using-model-for-processing-applications)
    * [Using array for processing applications](#using-array-for-processing-applications)
    * [Using hasReceivedApplicationFrom()](#using-hasreceivedapplicationfrom)
    * [Using receivedApplicationsFrom()](#using-receivedapplicationsfrom)
    * [Using receivedApplications()](#using-receivedapplications)
    * [Using receiverCriteria Attribute](#using-receivercriteria-attribute)
    * [Using setReceiverCriteria()](#using-setreceivercriteria)
* [Testing](#testing)

## Installation

You can install the package via composer:

```bash
composer require te7a-houdini/laravel-applicant
```

Then publish the configurations and migrations:

```bash
php artisan vendor:publish --provider="Te7aHoudini\LaravelApplicant\LaravelApplicantServiceProvider"
```

After the migration has been published then run the migrations to create required tables:

```bash
php artisan migrate
```

## Usage

let's assume we have `User model` & `Group model`.

### Using Applicant trait
to allow model behaves as applicant add `Te7aHoudini\LaravelApplicant\Traits\Applicant` trait to your model

``` php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Te7aHoudini\LaravelApplicant\Traits\Applicant;

class User extends Authenticatable
{
    use Applicant;

    // ...
}
```

to make `User` model applies for `Group` model and creates a new `application` record in `applications` table . we can do this by different ways:

### Using model for creating applications

if you didn't specify any application type or status. by default the `type` will be `applicant` and `status` wil be `created`

```php
//create a record with default type of "applicant" and default status of "created" 
$user->appliesFor($group);

//create a record with type of "randomAppType" and default status of "created" 
$user->appliesFor($group, ['type' => 'randomAppType']);

//create a record with type of "randomAppType" and status of "randomAppStatus" 
$user->appliesFor($group, ['type' => 'randomAppType', 'status' => 'randomAppStatus']);
```

### Using array for creating applications

in some cases you won't have a model object , but want to manually specify the attributes when creating application.

```php
$user->appliesFor([
    'receiver_id' => 1,
    'receiver_type' => 'App\Models\Group',
]);

$user->appliesFor([
    'receiver_id' => 1,
    'receiver_type' => 'App\Models\Group',
    'type' => 'randomAppType',
    'status' => 'randomAppStatus',
]);
```

### Using null for creating applications

if you don't want to specify a model for creating applications then no problem.

```php
//create a record with default type of "applicant" and default status of "created" 
//and both receiver_id & receiver_type are null
$user->appliesFor();
```

### Using hasAppliedFor()

if we want to check if model has made an application for another model. then we can achieve that with different ways.

 `note: like appliesFor() the hasAppliedFor() accepts same parameters`

```php
$user->hasAppliedFor($group);
$user->hasAppliedFor($group, ['type' => 'randomAppType', 'status' => 'randomAppStatus']);

$user->hasAppliedFor([
    'receiver_id' => 1,
    'receiver_type' => 'App\Models\Group',
]);

//check if $user model has application record with default type and status or not
$user->hasAppliedFor();
```

### Using appliedApplicationsFor()

if you want to get current model applied applications you can do like this:

`note: like appliesFor() the appliedApplicationsFor() accepts same parameters`

```php
$user->appliedApplicationsFor($group)->get();
$user->appliedApplicationsFor($group, ['type' => 'randomAppType', 'status' => 'randomAppStatus'])->get();

$user->appliedApplicationsFor([
    'receiver_id' => 1,
    'receiver_type' => 'App\Models\Group',
])->get();
```
### Using appliedApplications()

this is a <a href="https://laravel.com/docs/master/eloquent-relationships#one-to-many-polymorphic-relations" target="_blank">morphMany</a> relation between current model and `Application` model

```php
//returns \Illuminate\Database\Eloquent\Relations\MorphMany
$user->appliedApplications();
```
### Using applicantCriteria Attribute

some models maybe applies for specific application `type` or `status` , so to make it easy for overriding default application `type` and `status` . just define `applicantCriteria` attribute in your model 

``` php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Te7aHoudini\LaravelApplicant\Traits\Applicant;

class User extends Authenticatable
{
    use Applicant;

    //you can define type or status or both
    $this->applicantCriteria = [
        'type' => 'randomAppType',
        'status' => 'randomAppStatus',
    ];
}

//instead of this.
$user->appliesFor($group, ['type' => 'randomAppType', 'status' => 'randomAppStatus']);
$user->hasAppliedFor($group,['type' => 'randomAppType', 'status' => 'randomAppStatus']);
$user->appliedApplicationsFor($group, ['type' => 'randomAppType', 'status' => 'randomAppStatus'])->get();

//you can now just remove the second param.
//and by default this will set the type to "randomAppType" and status to "randomAppStatus"
$user->appliesFor($group);
$user->hasAppliedFor($group);
$user->appliedApplicationsFor($group)->get();
```

### Using setApplicantCriteria()

if you want to set the applicant criteria dynamically per model. you can do this.

``` php
//this will create a record with type of "randomAppType" and status of "randomAppStatus"
$user->setApplicantCriteria([
    'type' => 'randomAppType',
    'status' => 'randomAppStatus',
])->appliesFor($group);
```

### Using ReceivesApplications trait
to allow model behaves as receiver add `Te7aHoudini\LaravelApplicant\Traits\ReceivesApplications` trait to your model

``` php
use Illuminate\Database\Eloquent\Model;
use Te7aHoudini\LaravelApplicant\Traits\ReceivesApplications;

class Group extends Model
{
    use ReceivesApplications;

    // ...
}
```

to make `Group` model process application from `User` model and update existing `application` record in `applications` table . we can do this by different ways:

### Using model for processing applications

if you didn't specify any application type or status. by default will query of `type` will `applicant` and `status` of `created`

```php
//update existing record with default status of "processed" .
$group->processApplicationFrom($user);

//instead of updating with default status of "processed" then it will get updated by "accepted" status.
$group->processApplicationFrom($user, 'accepted');

//query by type of "randomAppType" and update status to "processed" 
$group->processApplicationFrom($user, ['type' => 'randomAppType']);

//query by type of "randomAppType" and update status to "accepted" 
$group->processApplicationFrom($user, ['type' => 'randomAppType'], 'accepted');

//query by type of "randomAppType" and status of "randomAppStatus" 
$group->processApplicationFrom($user, ['type' => 'randomAppType', 'status' => 'randomAppStatus']);

//query by type of "randomAppType" and status of "randomAppStatus" and update status to "accepted"
$group->processApplicationFrom($user, ['type' => 'randomAppType', 'status' => 'randomAppStatus'], 'accepted');
```

### Using array for processing applications

in some cases you won't have a model object , but want to manually specify the attributes when processing application.

```php
$group->processApplicationFrom([
    'applicant_id' => 1,
    'applicant_type' => 'App\Models\User',
]);

$user->processApplicationFrom([
    'applicant_id' => 1,
    'applicant_type' => 'App\Models\User',
    'type' => 'randomAppType',
    'status' => 'randomAppStatus',
]);

//you can override the default status of "processed" by providing second param.
$group->processApplicationFrom([
    'applicant_id' => 1,
    'applicant_type' => 'App\Models\User',
], 'accepted');
```

### Using hasReceivedApplicationFrom()

if we want to check if model has received an application from another model. then we can achieve that with different ways.

 `note: like processApplicationFrom() the hasReceivedApplicationFrom() accepts same parameters except the last parameter of newStatus`

```php
$group->hasReceivedApplicationFrom($user);
$group->hasReceivedApplicationFrom($user, ['type' => 'randomAppType', 'status' => 'randomAppStatus']);

$group->hasReceivedApplicationFrom([
    'applicant_id' => 1,
    'applicant_type' => 'App\Models\User',
]);
```

### Using receivedApplicationsFrom()

if you want to get current model received applications you can do like this:

 `note: like processApplicationFrom() the receivedApplicationsFrom() accepts same parameters except the last parameter of newStatus`

```php
$group->receivedApplicationsFrom($user)->get();
$group->receivedApplicationsFrom($user, ['type' => 'randomAppType', 'status' => 'randomAppStatus'])->get();

$group->receivedApplicationsFrom([
    'applicant_id' => 1,
    'applicant_type' => 'App\Models\User',
])->get();
```
### Using receivedApplications()

this is a <a href="https://laravel.com/docs/master/eloquent-relationships#one-to-many-polymorphic-relations" target="_blank">morphMany</a> relation between current model and `Application` model

```php
//returns \Illuminate\Database\Eloquent\Relations\MorphMany
$user->receivedApplications();
```
### Using receiverCriteria Attribute

some models maybe applies for specific application `type` or `status` , so to make it easy for overriding default application `type` and `status` . just define `receiverCriteria` attribute in your model 

``` php
use Illuminate\Database\Eloquent\Model;
use Te7aHoudini\LaravelApplicant\Traits\ReceivesApplications;

class Group extends Model
{
    use ReceivesApplications;

    //you can define type or status or both
    $this->receiverCriteria = [
        'type' => 'randomAppType',
        'status' => 'randomAppStatus',
    ];
}

//instead of this.
$group->processApplicationFrom($user, ['type' => 'randomAppType', 'status' => 'randomAppStatus']);
$group->hasReceivedApplicationFrom($user,['type' => 'randomAppType', 'status' => 'randomAppStatus']);
$group->receivedApplicationsFrom($user, ['type' => 'randomAppType', 'status' => 'randomAppStatus'])->get();

//you can now just remove the second param.
//and by default this will query by type of "randomAppType" and status of "randomAppStatus"
$group->processApplicationFrom($user);
$group->hasReceivedApplicationFrom($user);
$group->receivedApplicationsFrom($user)->get();
```

### Using setReceiverCriteria()

if you want to set the receiverCriteria criteria dynamically per model. you can do this.

``` php
//this will query by type of "randomAppType" and status of "randomAppStatus"
$user->setReceiverCriteria([
    'type' => 'randomAppType',
    'status' => 'randomAppStatus',
])->processApplicationFrom($group);
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

### Security

If you discover any security related issues, please email ahmedabdelftah95165@gmail.com instead of using the issue tracker.

## Credits

- [Ahmed Abd El Ftah](https://github.com/Te7a-Houdini)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
