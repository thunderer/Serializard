<?php
namespace Thunder\Serializard\Normalizer;

use Symfony\Component\Yaml\Yaml;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 * @codeCoverageIgnore
 */
final class JmsSerializerNormalizer
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function __invoke($var)
    {
        $data = file_get_contents($this->path);
        if(!$data) {
            throw new \RuntimeException(sprintf('Failed to read file at path %s!', $this->path));
        }

        $yaml = Yaml::parse($data);
        $keys = array_keys($yaml);
        $yaml = $yaml[$keys[0]];
        if(!$yaml) {
            throw new \InvalidArgumentException('No config at '.$this->path);
        }

        $hasPolicy = array_key_exists('exclusion_policy', $yaml);
        if(!$hasPolicy || ($hasPolicy && 'ALL' !== $yaml['exclusion_policy'])) {
            throw new \RuntimeException(sprintf('This serializer supports only ALL, %s given!', $yaml['exclusion_policy']));
        }

        $ref = new \ReflectionObject($var);
        $result = [];
        foreach($yaml['properties'] as $key => $config) {
            if($config['expose'] !== true) {
                continue;
            }

            $name = array_key_exists('serialized_name', $config) ? $config['serialized_name'] : $key;

            if($ref->hasProperty($key)) {
                $prop = $ref->getProperty($key);
                $prop->setAccessible(true);

                $result[$name] = $prop->getValue($var);

                continue;
            }

            $method = 'get'.ucfirst($key);
            if(method_exists($var, $method)) {
                $result[$name] = call_user_func_array([$var, $method], []);

                continue;
            }

            // no method or property found, skip current key
        }

        return $result;
    }
}
