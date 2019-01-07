# TimeClock

Simple work time clocking service built on [Yii 2 framework](https://www.yiiframework.com).

![screen](https://bizley.github.io/timeclock/timeclock.png)

## Installation

1. Install TimeClock using Composer:
  
    `composer create-project --prefer-dist bizley/timeclock timeclock`
    
2. Prepare virtual host pointing to `/public` directory.
3. Prepare configuration for DB of your choice. Place it in `/src/config/db.php`.
4. Modify the `/src/config/web.php` file to change:

    - `timeZone` (default `UTC`),
    - `language` (default `en-US`; `pl` translations are provided in `/src/messages/pl` folder),
    - `components > mailer` configuration to actually send emails (needed for password reset),
    - `params > company` (default `Company Name`; displayed in footer and other layout places),
    - `params > email` (default `email@company.com`; used as the email sender address for emails),
    - `params > allowedDomains` (default `['@company.com']`; array with email domains allowed for registration).
    
5. Change `/public/index.php` file to set `YII_DEBUG` mode to `false` and `YII_ENV` environment to `prod`.
6. Apply migrations by running in console `php yii migrate`.
7. Start website and register first account.
8. If you want to make an account to be admin run in console `php yii admin/set ID` where `ID` is DB identifier of account 
   to be set (usually first one is `1`).
   
## Ground rules

- Registering account requires its email address to be in one of the provided domains. If you want to change this behavior 
  you must prepare your own code. Current implementation is at `/src/models/RegisterForm.php` and `/src/views/site/register.php`.
- Session can be start at any time but it must be ended not overlapping any other ended session.
- There can be many sessions in one day.
- Session can not be longer than midnight.
- Not ended sessions not count for work hours.
- Off-time must not overlap any other off-time period.
- Holidays are automatically fetched from `https://www.kalendarzswiat.pl` which is Polish holiday list. If you want to 
  use something different you must prepare your own code for this. Current implementation is at `/src/models/Holiday.php`.

## Features

- account registration
- password reset
- profile update
- light and dark theme
- signing in with login or PIN
- session time with note
- off-time with note
- session and off-time history
- calendar
- holidays
- admin section
- REST API
  
## General help

Read [TimeClock Wiki](https://github.com/bizley/timeclock/wiki) first.

For anything related to Yii go to the [Yii 2 Guide](https://www.yiiframework.com/doc/guide/2.0/en).  
I really don't want to point obvious links with solutions from there.

## Usage of this project

You can use this project in whatever way you like as long as you mention where did you get it from.
