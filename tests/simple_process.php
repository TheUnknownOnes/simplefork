<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TUO\SimpleFork\Process;

class MyJob extends Process {
  public function run() {
    $cnt = 1;
    while ($cnt++ < 5) {
      sleep(1);
    }

    return 0;
  }
}

$Job = new MyJob();

$Job->start();

while ($Job->isRunning()) {
  usleep(1000000);
  echo "Waiting for Job ...." . PHP_EOL;
}

echo "Job exited with code {$Job->getErrorCode()}" . PHP_EOL;

?>