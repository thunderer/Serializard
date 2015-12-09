<?php
namespace Thunder\Serializard\Normalizer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class ReflectionNormalizer
{
    private $skipped;

    public function __construct(array $skipped = [])
    {
        $this->skipped = $skipped;
    }

    public function __invoke($var)
    {
        $ref = new \ReflectionObject($var);

        $result = [];
        foreach($ref->getProperties() as $property) {
            if(in_array($property->getName(), $this->skipped)) {
                continue;
            }

            $property->setAccessible(true);

            $result[$property->getName()] = $property->getValue($var);
        }

        return $result;
    }
}
