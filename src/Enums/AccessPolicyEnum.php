<?php

declare(strict_types=1);

namespace Sdpgs\Gyazo\Enums;

enum AccessPolicyEnum: string
{
    case ANYONE = 'anyone';
    case ONLY_ME = 'only_me';
}
