<?php

namespace App\Enums;

enum PostType: string
{
    case EXPERIENCE = 'experience';
    case QUESTION = 'question';

    public static function labels(): array
    {
        return [
            self::EXPERIENCE->value => 'Erfahrungen',
            self::QUESTION->value => 'Fragen',
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getLabel(string $value): string
    {
        return self::labels()[$value] ?? '';
    }
}
