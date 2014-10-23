<?php

namespace Ray\Aop\Sample;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocationInterface;

class WeekendBlocker implements MethodInterceptor
{
    public function invoke(MethodInvocationInterface $invocation)
    {
        $today = getdate();
        if ($today['weekday'][0] === 'S') {
            throw new \RuntimeException(
                  $invocation->getMethod()->getName() . " not allowed on weekends!"
            );
        }
        return $invocation->proceed();
    }
}
