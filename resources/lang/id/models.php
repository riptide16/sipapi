<?php

return [
    'user' => [
        'status' => [
            \App\Models\User::STATUS_ACTIVE => 'Aktif',
            \App\Models\User::STATUS_INACTIVE => 'Menunggu Verifikasi',
            \App\Models\User::STATUS_FAILED => 'Ditolak',
        ],
    ],
];
