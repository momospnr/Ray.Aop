<?php

namespace Ray\Aop;

use Ray\Aop\Sample\WeekendBlocker;

require dirname(__DIR__) . '/bootstrap.php';

$bind = (new Bind)->bindInterceptors('chargeOrder', [new WeekendBlocker]);
$compiler = new Compiler($_ENV['TMP_DIR']);
$billingService = $compiler->newInstance('Ray\Aop\Sample\RealBillingService', [], $bind);

try {
    echo $billingService->chargeOrder();
} catch (\RuntimeException $e) {
    echo $e->getMessage() . "\n";
    exit(1);
}
