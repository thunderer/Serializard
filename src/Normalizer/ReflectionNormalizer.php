<?php
namespace Thunder\Serializard\Normalizer;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class ReflectionNormalizer
{
    private $skipped;

    public function __construct(array $skipped = array())
    {
        $this->skipped = $skipped;
    }

    public function __invoke($var)
    {
        $ref = new \ReflectionObject($var);

        $result = array();
        while($ref) {
            foreach($ref->getProperties() as $property) {
                if(in_array($property->getName(), $this->skipped, true)) {
                    continue;
                }

                $property->setAccessible(true);

                $result[$property->getName()] = $property->getValue($var);
            }

            $ref = $ref->getParentClass();
        }

        return $result;
    }
}
