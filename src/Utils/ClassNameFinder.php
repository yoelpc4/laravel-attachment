<?php

namespace Yoelpc4\LaravelAttachment\Utils;

final class ClassNameFinder
{
    /**
     * @var object
     */
    private static $composer = null;

    /**
     * @var array
     */
    private static $classNames = [];

    /**
     * ClassFinder constructor.
     */
    public function __construct()
    {
        self::$composer = require base_path().'/vendor/autoload.php';

        if (false === empty(self::$composer)) {
            self::$classNames = array_keys(self::$composer->getClassMap());
        }
    }

    /**
     * Get class names from class map
     *
     * @return array
     */
    public function getClassNames()
    {
        $allClasses = [];

        if (false === empty(self::$classNames)) {
            foreach (self::$classNames as $class) {
                $allClasses[] = '\\'.$class;
            }
        }

        return $allClasses;
    }

    /**
     * Get class names by namespace from class map
     *
     * @param $namespace
     * @return array
     */
    public function getClassNamesByNamespace($namespace)
    {
        if (0 !== strpos($namespace, '\\')) {
            $namespace = '\\'.$namespace;
        }

        $termUpper = strtoupper($namespace);

        return array_filter($this->getClassNames(), function ($class) use ($termUpper) {
            $className = strtoupper($class);
            if (
                0 === strpos($className, $termUpper) and
                false === strpos($className, strtoupper('Abstract')) and
                false === strpos($className, strtoupper('Interface'))
            ) {
                return $class;
            }

            return false;
        });
    }

    /**
     * Get class names with term from class map
     *
     * @param $term
     * @return array
     */
    public function getClassNamesWithTerm($term)
    {
        $termUpper = strtoupper($term);

        return array_filter($this->getClassNames(), function ($class) use ($termUpper) {
            $className = strtoupper($class);
            if (
                false !== strpos($className, $termUpper) and
                false === strpos($className, strtoupper('Abstract')) and
                false === strpos($className, strtoupper('Interface'))
            ) {
                return $class;
            }

            return false;
        });
    }
}
