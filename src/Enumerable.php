<?php
declare(strict_types=1);
namespace ParagonIE\Paseto;

interface Enumerable
{
    /**
     * PHPs type system can't enforce this, but `self`
     * below should be restricted to instances of the implementing
     * class, thus a LogicException should be thrown if two
     * types are attempted to be compared that are not equal.
     *
     * Return true if instances $a and $b are in equal states.
     *
     * @param Enumerable $a
     * @param Enumerable $b
     * @return bool
     */
    public static function equalStates(self $a, self $b): bool;

    /**
     * Return an array containing all possible states.
     *
     * @return array<int, self>
     */
    public static function enumerate(): array;
}
