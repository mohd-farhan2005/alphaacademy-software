<?php

namespace App\Enums;

enum RoleType: string
{
    case SUPER_ADMIN = 'super_admin';
    case DME_HEAD = 'dme_head';
    case HA_HEAD = 'ha_head';
    case CREATIVES_HEAD = 'creatives_head';
    case EMPLOYEE = 'employee';
}
