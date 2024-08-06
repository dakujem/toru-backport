<?php

declare(strict_types=1);

namespace Dakujem\Toru;

/**
 * A trivial generic processing pipeline implementation.
 *
 * The input is processed by the first stage, the result is passed on to the second and so on.
 * Each stage receives the output of the previous stage.
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
final class Pipeline
{
    /**
     * @param mixed $passable
     * @param callable ...$stages
     * @return mixed
     */
    public static function through($passable, callable ...$stages)
    {
        foreach ($stages as $stage) {
            $passable = $stage($passable);
        }
        return $passable;
    }

    /**
     * @param mixed $passable
     * @param callable[]|iterable $stages
     * @return mixed
     */
    public static function throughStages($passable, iterable $stages)
    {
        return self::through($passable, ...$stages);
    }
}
