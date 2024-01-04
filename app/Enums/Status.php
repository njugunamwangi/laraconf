<?php

    namespace App\Enums;

    enum Status: string {
        case Archived = 'Archived';
        case Published = 'Published';
        case Draft = 'Draft';
    }