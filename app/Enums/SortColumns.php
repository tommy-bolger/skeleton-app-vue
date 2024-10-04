<?php

namespace App\Enums;

enum SortColumns: string
{
    case NAME = 'name';
    case DESCRIPTION = 'description';
    case AUTHOR_EMAIL = 'author_email';
    case SLUG = 'slug';
}
