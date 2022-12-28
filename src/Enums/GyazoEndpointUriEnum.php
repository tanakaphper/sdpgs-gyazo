<?php

declare(strict_types=1);

namespace Sdpgs\Gyazo\Enums;

enum GyazoEndpointUriEnum: string
{
    case LIST = 'https://api.gyazo.com/api/images';
    case UPLOAD = 'https://upload.gyazo.com/api/upload';
    case IMAGE = 'https://api.gyazo.com/api/images/';
    case O_EMBED = 'https://api.gyazo.com/api/oembed';
}
