<?php

return [
    'archive_path' => env('DOWNLOAD_ARCHIVE_PATH', storage_path('app/downloads/Site360Pro-v1.0.8-full.zip'),),
    'filename' => env('DOWNLOAD_ARCHIVE_NAME', 'Site360Pro-v1.0.8-full.zip'),
    'password_hash' => env('DOWNLOAD_PASSWORD_HASH'),
];
