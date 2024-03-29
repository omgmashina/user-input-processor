<?php

declare(strict_types=1);

namespace Flaksp\UserInputProcessor\Denormalizer;

use Flaksp\UserInputProcessor\ConstraintViolation\ConstraintViolationCollection;
use Flaksp\UserInputProcessor\ConstraintViolation\NumberIsTooBig;
use Flaksp\UserInputProcessor\ConstraintViolation\NumberIsTooSmall;
use Flaksp\UserInputProcessor\ConstraintViolation\WrongPropertyType;
use Flaksp\UserInputProcessor\Exception\ValidationError;
use Flaksp\UserInputProcessor\Pointer;
use LogicException;

final class IntegerDenormalizer
{
    /**
     * @throws ValidationError If $data has invalid parameters
     */
    public function denormalize(
        mixed $data,
        Pointer $pointer,
        int $minimum = null,
        int $maximum = null,
    ): int {
        if (null !== $minimum && null !== $maximum && $minimum > $maximum) {
            throw new LogicException('Minimum constraint can not be bigger than maximum');
        }

        $violations = new ConstraintViolationCollection();

        if (!\is_int($data)) {
            $violations[] = WrongPropertyType::guessGivenType(
                $pointer,
                $data,
                [WrongPropertyType::JSON_TYPE_INTEGER]
            );

            throw new ValidationError($violations);
        }

        if (null !== $minimum && $data < $minimum) {
            $violations[] = new NumberIsTooSmall(
                $pointer,
                $minimum
            );
        }

        if (null !== $maximum && $data > $maximum) {
            $violations[] = new NumberIsTooBig(
                $pointer,
                $maximum
            );
        }

        if ($violations->isNotEmpty()) {
            throw new ValidationError($violations);
        }

        return $data;
    }
}
