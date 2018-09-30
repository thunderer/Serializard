<?php
namespace Thunder\Serializard\Normalizer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class ClosureBindNormalizer
{
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function __invoke($var)
    {
        return \call_user_func(\Closure::bind($this->callback, $var, $var), $var);
    }
}
