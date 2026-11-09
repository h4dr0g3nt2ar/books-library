<?php

declare(strict_types=1);

namespace App\Domain\Book\Enum;

enum BindingType: string
{
    case Hardcover = 'hardcover';
    case Paperback = 'paperback';

    public function label(): string
    {
        return match($this) {
            self::Hardcover => 'Твёрдый переплет',
            self::Paperback => 'Мягкий переплет',
        };
    }
}