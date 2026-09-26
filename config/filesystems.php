<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Default Filesystem Disk
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default filesystem disk that should be used
	| by the framework. A "local" driver, as well as a variety of cloud
	| based drivers are available for your choosing. Just store away!
	|
	| Supported: "local", "s3", "rackspace"
	|
	*/

	'default' => 'local',

	/*
	|--------------------------------------------------------------------------
	| Default Cloud Filesystem Disk
	|--------------------------------------------------------------------------
	|
	| Many applications store files both locally and in the cloud. For this
	| reason, you may specify a default "cloud" driver here. This driver
	| will be bound as the Cloud disk implementation in the container.
	|
	*/

	'cloud' => 's3',

	/*
	|--------------------------------------------------------------------------
	| Filesystem Disks
	|--------------------------------------------------------------------------
	|
	| Here you may configure as many filesystem "disks" as you wish, and you
	| may even configure multiple disks of the same driver. Defaults have
	| been setup for each driver as an example of the required options.
	|
	*/

	'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path().'/app',
        ],

        // STORAGE_PROVIDER ("spaces", the default, or "azure_blob") is read
        // at request time rather than baked into a single driver choice
        // because the same built image is deployed to both DOKS (Spaces)
        // and AKS (Blob) at once during the DO->Azure migration coexistence
        // window — see gitops' docs/migration-steps.md §3, and cobalt's
        // config.StorageProvider() / mithril's storage.rs for the same
        // switch in the other two backends.
        'public' => array_merge(
            [
                'driver' => env('STORAGE_PROVIDER', 'spaces') === 'azure_blob' ? 'azure-storage-blob' : 's3',
                'visibility' => 'public',
            ],
            env('STORAGE_PROVIDER', 'spaces') === 'azure_blob'
                ? [
                    // Shared Key auth, matching cobalt/mithril's
                    // AZURE_STORAGE_ACCOUNT/AZURE_STORAGE_KEY vars.
                    'credential' => 'shared_key',
                    'account_name' => env('AZURE_STORAGE_ACCOUNT', ''),
                    'account_key' => env('AZURE_STORAGE_KEY', ''),
                    'container' => env('AZURE_STORAGE_CONTAINER', 'vatusa-storage'),
                    'is_public_container' => true,
                ]
                : [
                    'key'    => env('DO_SPACES_KEY', ''),
                    'secret' => env('DO_SPACES_SECRET', ''),
                    'endpoint' => 'https://nyc3.digitaloceanspaces.com',
                    'region' => env('DO_SPACES_REGION', 'nyc3'),
                    'bucket' => env('DO_SPACES_BUCKET', 'vatusa-storage'),
                ]
        ),

		'rackspace' => [
			'driver'    => 'rackspace',
			'username'  => 'your-username',
			'key'       => 'your-key',
			'container' => 'your-container',
			'endpoint'  => 'https://identity.api.rackspacecloud.com/v2.0/',
			'region'    => 'IAD',
		],

	],

];
