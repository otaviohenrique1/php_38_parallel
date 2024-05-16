<?php

use \parallel\Runtime;

$runtime = new Runtime();

$runtime->run(function () {
  // var_dump("Tarefa 2:", debug_backtrace());
  echo 'Executando terefa demorada 2' . PHP_EOL;
  sleep(8);
  echo 'Finalizado terefa demorada 2' . PHP_EOL;
});

$runtime2 = new Runtime();
$runtime2->run(function () {
  echo 'Executando terefa demorada 3' . PHP_EOL;
  sleep(5);
  echo 'Finalizado terefa demorada 3' . PHP_EOL;
});

// var_dump("Tarefa 1:", debug_backtrace());
echo 'Executando terefa demorada 1' . PHP_EOL;
sleep(10);
echo 'Finalizado terefa demorada 1' . PHP_EOL;
