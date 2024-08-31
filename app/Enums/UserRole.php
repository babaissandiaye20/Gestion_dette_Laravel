<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Boutiquier = 'boutiquier';
    case Client = 'client';
}
