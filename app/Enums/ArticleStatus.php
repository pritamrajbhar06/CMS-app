<?php

namespace App\Enums;

use Henzeb\Enumhancer\Concerns\Enhancers;

enum ArticleStatus: int
{
    use Enhancers;

    case Draft = 0;
    case Published = 1;
    case Archived = 2;

    public static function value(?string $status = null): ?int
    {
        foreach (self::cases() as $case) {
            if ($case->name === $status) {
                return $case->value;
            }
        }

        return null; // or throw an exception if preferred
    }

}
