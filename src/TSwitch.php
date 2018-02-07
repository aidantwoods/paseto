<?php
declare(strict_types=1);
namespace ParagonIE\Paseto;

/**
 * TSwitch<E, R>
 */
abstract class TSwitch
{
    /**
     * @param E
     * @param TCase<E, R>
     * @return R
     */
    /** @psalm-suppress MissingReturnType */
    public static function over(Enumerable $e, TCase ...$cases)
    {
        $states = $e::enumerate();
        /** @var array<int, TCase> */
        $approvedCases = [];
        /** @var TCase|null */
        $matchedCase = null;

        if (\count($states) !== \count($cases)) {
            throw new \LogicException('Must have as many cases as states');
        }

        foreach ($states as $state) {
            /** @var array<int, TCase> */
            $matchingCases = [];
            foreach ($cases as $case) {
                $caseEnum = $case->getEnumerable();

                if (!(($state instanceof $caseEnum) && ($caseEnum instanceof $state))) {
                    throw new \LogicException('States and cases must use the same enumerable');
                }
                if ($state::equalStates($state, $caseEnum)) {
                    $matchingCases[] = $case;

                    if ($state::equalStates($e, $state)) {
                        if (isset($matchedCase)) {
                            throw new \LogicException('Multiple matches have occured');
                        }
                        $matchedCase = $case;
                    }
                }
            }
            if (\count($matchingCases) !== 1) {
                throw new \LogicException('No matches were found');
            }

            $approvedCases[] = \array_shift($matchingCases);
        }

        if (\count($states) !== \count($approvedCases)) {
            throw new \LogicException('Something went very wrong');
        }

        if (!isset($matchedCase)) {
            throw new \LogicException('Nothing matched the given data');
        }

        return $matchedCase->run();
    }

}
