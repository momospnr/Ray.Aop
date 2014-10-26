<?php
/**
 * This file is part of the Ray.Aop package
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Ray\Aop;

use ReflectionClass;
use ReflectionMethod;

final class Bind
{
    /**
     * @var array
     */
    public $bindings = [];

    /**
     * {@inheritdoc}
     */
    public function bind($class, array $pointcuts)
    {
        foreach ($pointcuts as $pointcut) {
            /** @var $pointcut Pointcut */
            $this->bindPointcut(new \ReflectionClass($class), $pointcut);
        }
    }

    /**
     * @param ReflectionClass $class
     * @param Pointcut        $pointcut
     */
    private function bindPointcut(\ReflectionClass $class, Pointcut $pointcut)
    {
        $isClassMatch = $pointcut->classMatcher->matchesClass($class, $pointcut->classMatcher->getArguments());
        if ($isClassMatch === false) {

            return;
        }
        $this->methodsMatch($class, $pointcut->methodMatcher, $pointcut->interceptors);
    }

    /**
     * @param ReflectionClass $class
     * @param AbstractMatcher $methodMatcher
     * @param Interceptor[]   $interceptors
     */
    private function methodsMatch(\ReflectionClass $class, AbstractMatcher $methodMatcher, array $interceptors)
    {
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $this->methodMatch($method, $methodMatcher, $interceptors);
        }
    }

    /**
     * @param ReflectionMethod $method
     * @param AbstractMatcher  $methodMatcher
     * @param Interceptor[]    $interceptors
     */
    private function methodMatch(\ReflectionMethod $method, AbstractMatcher $methodMatcher, array $interceptors)
    {
        $isMethodMatch = $methodMatcher->matchesMethod($method, $methodMatcher->getArguments());
        if ($isMethodMatch) {
            $this->bindInterceptors($method->name, $interceptors);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function bindInterceptors($method, array $interceptors)
    {
        $this->bindings[$method] = !isset($this->bindings[$method]) ? $interceptors : array_merge($this->bindings[$method], $interceptors);

        return $this;
    }

    public function __toString()
    {
        return md5(serialize($this->bindings));
    }
}
