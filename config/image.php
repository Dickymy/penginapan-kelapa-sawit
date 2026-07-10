<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Upload Limits
    |--------------------------------------------------------------------------
    */

    'upload_max_mb' => (int) env('IMAGE_UPLOAD_MAX_MB', 15),

    /*
    |--------------------------------------------------------------------------
    | Processing
    |--------------------------------------------------------------------------
    */

    'max_width' => (int) env('IMAGE_MAX_WIDTH', 2560),
    'max_height' => (int) env('IMAGE_MAX_HEIGHT', 1920),

    /*
    |--------------------------------------------------------------------------
    | Quality (1-100)
    |--------------------------------------------------------------------------
    */

    'full_quality' => (int) env('IMAGE_FULL_QUALITY', 82),
    'thumb_quality' => (int) env('IMAGE_THUMB_QUALITY', 78),

    /*
    |--------------------------------------------------------------------------
    | Variants
    |--------------------------------------------------------------------------
    | Each variant will be created during upload.
    | Key = variant name, width = max width, height = max height.
    */

    'variants' => [
        'thumb' => ['width' => 480, 'height' => 360, 'quality' => (int) env('IMAGE_THUMB_QUALITY', 78)],
        'medium' => ['width' => 960, 'height' => 720, 'quality' => (int) env('IMAGE_FULL_QUALITY', 82)],
        'large' => ['width' => 1920, 'height' => 1440, 'quality' => (int) env('IMAGE_FULL_QUALITY', 82)],
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed MIME Types
    |--------------------------------------------------------------------------
    */

    'allowed_mimes' => ['image/jpeg', 'image/png', 'image/webp'],

    /*
    |--------------------------------------------------------------------------
    | Output Format
    |--------------------------------------------------------------------------
    | Preferred output format. 'webp' recommended for web performance.
    | Fallback to 'jpeg' if WebP is not supported by the image driver.
    */

    'output_format' => env('IMAGE_OUTPUT_FORMAT', 'webp'),

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    | 'gd' or 'imagick'. GD is available on most PHP installations.
    */

    'driver' => env('IMAGE_DRIVER', 'gd'),

];
