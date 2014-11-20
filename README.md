SoftLayer Message Queue driver for Laravel
==========================================

[![License](http://img.shields.io/packagist/l/nathanmac/laravel-queue-softlayer.svg)](https://github.com/nathanmac/laravel-queue-softlayer/blob/master/LICENSE.md)
[![Build Status](https://travis-ci.org/nathanmac/laravel-queue-softlayer.svg?branch=master)](https://travis-ci.org/nathanmac/laravel-queue-softlayer)

## Installation

Require this package in your composer.json and run composer update:

	"nathanmac/laravel-queue-softlayer": "1.*"
    
or run:

	composer require "nathanmac/laravel-queue-softlayer"

After composer update is finished you need to add ServiceProvider to your `providers` array in app.php:
				
   
	'Nathanmac\LaravelQueueSoftLayer\LaravelQueueSoftLayerServiceProvider',
	


now you are able to configure your connections in queue.php:

	return [
	
		'default'     => 'softlayer',
	
		'connections' => [
	
			'softlayer' => [
				'driver'         => 'softlayer',
		
		        'account'        => '', // SoftLayer Queue Account
				'username'       => '', // SoftLayer Username
				'token'          => '', // SoftLayer Password
	
				'queue'          => '', // name of the default queue
				
				 // Optional configuration settings for SoftLayer
                'endpoint'       => 'dal05',
                'private'        => true
			],
	
		],
	
	];

## Usage
Once you completed the configuration you can use Laravel Queue API. If you used other queue drivers you do not
need to change anything else. If you do not know how to use Queue API, please refer to the official Laravel
documentation: http://laravel.com/docs/queues


