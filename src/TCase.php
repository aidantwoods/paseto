<?php
declare(strict_types=1);
namespace ParagonIE\Paseto;

/**
 * TCase<E, R>
 */
class TCase
{
    /**
     * @var callable (): R
     */
    private $case;

    /**
     * @var Enumerable E
     */
    private $enumerable;

    /**
     * @param Enumerable $e E
     * @param closure $case (): R
     */
    public function __construct(Enumerable $e, callable $case)
    {
        $this->case = $case;
        $this->enumerable = $e;
    }

    /**
     * @return E
     */
    /**  */
    public function getEnumerable(): Enumerable
    {
        return $this->enumerable;
    }

    /**
     * @return R
     */
    /** @psalm-suppress MissingReturnType */
    public function run()
    {
        return ($this->case)();
    }
}
