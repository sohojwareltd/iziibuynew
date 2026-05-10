<?php

return [
    'default_avatar' => env('IZIIBUY_DEFAULT_AVATAR', 'users/default.png'),
    'storage_disk' => env('IZIIBUY_STORAGE_DISK', env('FILESYSTEM_DISK', 'public')),
];
