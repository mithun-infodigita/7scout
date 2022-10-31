<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'backup' => [
            'driver' => 'sftp',
            'host' => 'hmmusic.dyndns.org',
            'username' => 'backup7',
            'password' => 'zA79-JGTofkwMUbd',
            'port' => 1848,
            'root' => '/home',
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'producers' => [
            'driver' => 'local',
            'root' => storage_path('app/public/producers'),
            'url' => env('APP_URL').'/storage/producers',
            'visibility' => 'public',
        ],

        'temporaryFiles' => [
            'driver' => 'local',
            'root' => storage_path('temporaryFiles')
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
        ],


        'diebold' => [
            'driver' => 'ftp',
            'host' => 'sl1968.web.hostpoint.ch',
            'username' => 'diebold_ftp@seventools.com',
            'password' => 'vUYDo3GpToc2TgAj',
            'port' => 21,
        ],

        'amf' => [
            'driver' => 'ftp',
            'host' => 'sl1968.web.hostpoint.ch',
            'username' => 'amf_ftp@seventools.com',
            'password' => '4PgQ?H8Xg9kd*xRf*LDN',
            'port' => 21,
        ],

        'diametalImages' => [
            'driver' => 'ftp',
            'host' => 'sl1968.web.hostpoint.ch',
            'username' => 'diametal_ftp@seventools.com',
            'password' => 'xnnHEessNE9Xx@b3tXZN',
            'port' => 21,
            'root' => 'images',
        ],

        'localDiametalPartImages' => [
            'driver' => 'local',
            'root' => storage_path('/app/public/producers/diametal/partImages')
        ],
        'localDiametalPdfs' => [
            'driver' => 'local',
            'root' => storage_path('/app/public/producers/diametal/pdfs')
        ],

        'diametalMasterData' => [
            'driver' => 'ftp',
            'host' => 'sl1968.web.hostpoint.ch',
            'username' => 'diametal_ftp@seventools.com',
            'password' => 'xnnHEessNE9Xx@b3tXZN',
            'port' => 21,
            'root' => 'master_data',
        ],

        'localDiametalMasterData' => [
            'driver' => 'local',
            'root' => storage_path('/app/public/producers/diametal/masterData')
        ],

        'diametalStockData' => [
            'driver' => 'ftp',
            'host' => 'sl1968.web.hostpoint.ch',
            'username' => 'diametal_ftp@seventools.com',
            'password' => 'xnnHEessNE9Xx@b3tXZN',
            'port' => 21,
            'root' => 'stock',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
