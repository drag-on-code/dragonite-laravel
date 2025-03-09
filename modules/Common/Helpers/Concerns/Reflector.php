<?php

namespace Dragonite\Common\Helpers\Concerns;

use ReflectionClass;

trait Reflector
{
    /**
     * @return mixed[]
     */
    public function getFunctions($payload): ?array
    {
        $f = new ReflectionClass($payload);
        $methods = [];
        foreach ($f->getMethods() as $m) {
            if ($m->class == $payload) {
                $methods[] = $m->name;
            }
        }

        return $methods;
    }

    public function getClassFullNameFromFile($filePathName): ?string
    {
        return $this->getClassNamespaceFromFile($filePathName).'\\'.$this->getClassNameFromFile($filePathName);
    }

    public function getClassObjectFromFile($filePathName)
    {
        $classString = $this->getClassFullNameFromFile($filePathName);

        return new $classString;
    }

    public function getClassNamespaceFromFile($filePathName): ?string
    {
        $src = file_get_contents($filePathName);

        $tokens = token_get_all($src);
        $count = count($tokens);
        $i = 0;
        $namespace = '';
        $namespace_ok = false;
        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                // Found namespace declaration
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace_ok = true;
                        $namespace = trim($namespace);
                        break;
                    }

                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }

                break;
            }

            $i++;
        }

        if (! $namespace_ok) {
            return null;
        }

        return $namespace;
    }

    public function getClassNameFromFile($filePathName): ?string
    {
        $php_code = file_get_contents($filePathName);

        $classes = [];
        $tokens = token_get_all($php_code);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if (
                $tokens[$i - 2][0] == T_CLASS
                && $tokens[$i - 1][0] == T_WHITESPACE
                && $tokens[$i][0] == T_STRING
            ) {

                $class_name = $tokens[$i][1];
                $classes[] = $class_name;
            }
        }

        return $classes[0];
    }
}
