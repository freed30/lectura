<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Book Upload Disk
    |--------------------------------------------------------------------------
    |
    | Ce disque sert au stockage des livres apres upload admin. En local,
    | "public" fonctionne bien. En production, vous pouvez le remplacer
    | par "s3" ou un autre disque Laravel persistant.
    |
    */

    'book_upload_disk' => env('BOOK_UPLOAD_DISK', 'public'),
];
