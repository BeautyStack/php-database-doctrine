<?php

/*
 * Copyright © 2017-present The Stack World. All rights reserved.
 */

declare(strict_types=1);

namespace Beautystack\Database\Doctrine\Type;

use Beautystack\Database\Symfony\Type\Exception\InvalidTypeException;
use Beautystack\Value\Implementation\Money\Currency;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class CurrencyType extends StringType
{
    public const MY_TYPE = 'value_currency';

    public function convertToPHPValue($value, AbstractPlatform $platform): Currency | null
    {
        if ($value === null) {
            return null;
        }

        return Currency::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string | null
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Currency) {
            return parent::convertToDatabaseValue($value->getValue(), $platform);
        }
        if (is_string($value)) {
            return parent::convertToDatabaseValue($value, $platform);
        }

        throw new InvalidTypeException('Cannot put value in database it is not of type Slug');
    }

    public function getName(): string
    {
        return self::MY_TYPE;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
