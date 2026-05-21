<?php

namespace App\Enum;

enum Status: string
{
    case ACTIVE = 'active';
    case BLOCKED = 'blocked';
    case ARCHIVED = 'archived';
}
