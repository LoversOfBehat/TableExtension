<?php

declare(strict_types = 1);

namespace LoversOfBehat\TableExtension\Hook\Context\Annotation;

use Behat\Behat\Context\Annotation\AnnotationReader;
use LoversOfBehat\TableExtension\Hook\Call\AfterTableFetch;
use ReflectionMethod;

/**
 * This class matches hook annotations to the hook callees.
 *
 * @see \Behat\Behat\Hook\Context\Annotation\HookAnnotationReader
 */
final class HookAnnotationReader implements AnnotationReader
{

    /*
     * @var string
     */
    private static $regex = '/^\@(aftertablefetch)(?:\s+(.+))?$/i';

    /**
     * @var string[]
     */
    private static $classes = array(
        'aftertablefetch'    => AfterTableFetch::class,
    );

    /**
     * {@inheritdoc}
     */
    public function readCallee($contextClass, ReflectionMethod $method, $docLine, $description)
    {
        if (!preg_match(self::$regex, $docLine, $match)) {
            return null;
        }

        $type = strtolower($match[1]);
        $class = self::$classes[$type];
        $pattern = isset($match[2]) ? $match[2] : null;
        $callable = array($contextClass, $method->getName());

        return new $class($pattern, $callable, $description);
    }
}
