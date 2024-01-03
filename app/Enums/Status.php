<?php

    namespace App\Enums;

    enum Status: string {
        case ARCHIVED = 'Archived';
        case PUBLISHED = 'Published';
        case DRAFT = 'Draft';
    }