<?php
namespace Thunder\Serializard\Normalizer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class GetObjectVarsNormalizer
{
    public function __invoke($var)
    {
        return get_object_vars($var);
    }
}
