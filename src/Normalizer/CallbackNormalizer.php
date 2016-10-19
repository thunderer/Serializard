<?php
namespace Thunder\Serializard\Normalizer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class CallbackNormalizer
{
    private $callback;

    public function __construct($callback)
    {
        if(false === is_callable($callback)) {
            throw new \InvalidArgumentException('Given callback is not callable!');
        }

        $this->callback = $callback;
    }

    public function __invoke($var)
    {
        return call_user_func_array($this->callback, array($var));
    }
}
