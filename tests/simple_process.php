<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TUO\SimpleFork\Process;

class MyJob extends Process {
  public function run() {
    sleep(rand(1, 4));
    exit(rand(0, 32));
  }
}

$Job = new MyJob(true);

while ($Job->isRunning()) {
  usleep(1000000);
  echo "Waiting for Job ...." . PHP_EOL;
}

echo "Job exited with code {$Job->getErrorCode()} ({$Job->getErrorMessage()})" . PHP_EOL;

?>