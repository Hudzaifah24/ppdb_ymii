<?php

return [
    'auth_key' => base64_encode(env('SECRET_KEY_XENDIT') . ':'),
];
