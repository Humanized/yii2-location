# Yii2-Location - README
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)

Provides various interfaces to deal with routine location management tasks.

## Features

## Dependencies



## Installation

### Install Using Composer

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require humanized/yii2-location "dev-master"
```

or add

```
"humanized/yii2-location": "dev-master"
```

to the ```require``` section of your `composer.json` file.


### Add Module to Configuration

Add following lines to the configuration file:

```php
'modules' => [
    'contact' => [
        'class' => 'humanized\location\Module',
    ],
],
```

### Run Migrations 

```bash
$ php yii migrate/up --migrationPath=@vendor/humanized/yii2-location/migrations
```

## Module Configuration Options

### Global Configuration Options


### Grid Configuration Options

### CLI Configuration Options

### RBAC Integration

## Graphical User Interface (GUI)

Following controller actions are supported for now:

## Command Line Interface (CLI)

Following console commands are supported for now:


## REST Interface (API)

Due before version 0.5