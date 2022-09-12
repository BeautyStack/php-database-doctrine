<?php

/*
 * Copyright © 2017-present The Stack World. All rights reserved.
 */

declare(strict_types=1);

namespace Beautystack\Database\Doctrine\Type;

use Beautystack\Database\Symfony\Type\Exception\InvalidTypeException;
use Beautystack\Value\Implementation\DateTime\DateTimeUtc;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;

class DateTimeUtcType extends DateTimeType
{
    public const MY_TYPE = 'value_date_time';

    /**
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): DateTimeUtc | null
    {
        if ($value === null) {
            return null;
        }

        /** @var \DateTime $dateTime */
        $dateTime = parent::convertToPHPValue($value, $platform);

        return DateTimeUtc::fromPhpDateTime($dateTime);
    }

    /**
     * @throws InvalidTypeException
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string | null
    {
        if ($value === null) {
            return null;
        }

        if (! $value instanceof DateTimeUtc) {
            throw new InvalidTypeException('Cannot put value in database it is not of type DateTimeUtc');
        }

        return parent::convertToDatabaseValue($value->toPhpDateTime(), $platform);
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
