<?php
namespace App\Enum;


enum StoreUserRole:string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
}