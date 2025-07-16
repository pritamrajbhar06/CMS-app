<?php

namespace App\Enums;

use Henzeb\Enumhancer\Concerns\Enhancers;

enum ArticleStatus: int
{
    use Enhancers;

    case Draft = 1;
    case Published = 2;
    case Archived = 3;

    public static function value(?string $status = null): ?int
    {
        foreach (self::cases() as $case) {
            if ($case->name === $status) {
                return $case->value;
            }
        }

        return null; // or throw an exception if preferred
    }


    public static function label(int $value): string
    {
        return match ($value) {
            self::Draft->value => 'Draft',
            self::Published->value => 'Published',
            self::Archived->value => 'Archived',
            default => 'Unknown Status',
        };
    }

}
