<?php

declare(strict_types=1);

namespace Sdpgs\Gyazo\Enums;

enum GyazoEndpointUriEnum: string
{
    case LIST = 'https://api.gyazo.com/api/images';
    case UPLOAD = 'https://upload.gyazo.com/api/upload';
    case DELETE = 'https://api.gyazo.com/api/images/';
}
