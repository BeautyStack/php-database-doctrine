<?php

/*
 * Copyright © 2017-present The Stack World. All rights reserved.
 */

declare(strict_types=1);

namespace Beautystack\Database\Doctrine\Type;

use Beautystack\Database\Symfony\Type\Exception\InvalidTypeException;
use Beautystack\Value\Implementation\Identity\Id;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class IdType extends GuidType
{
    public const MY_TYPE = 'value_id';

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array                                     $fieldDeclaration the field declaration
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform         the currently used database platform
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return parent::getSQLDeclaration($fieldDeclaration, $platform);
    }

    /**
     * @param mixed $value
     *
     * @return Id|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): Id | null
    {
        if ($value === null) {
            return null;
        }

        return Id::fromString($value);
    }

    /**
     * @param mixed $value
     *
     * @return string|null
     *
     * @throws InvalidTypeException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string | null
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Id) {
            return parent::convertToDatabaseValue($value->getValue(), $platform);
        }
        if (is_string($value)) {
            return parent::convertToDatabaseValue($value, $platform);
        }
        throw new InvalidTypeException('Cannot put value in database it is not of type Id');
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
