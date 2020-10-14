<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TUO\SimpleFork\Process;
use TUO\SimpleFork\ProcessQueue;

class MyJob extends Process {
  public function run() {
    sleep(rand(1, 4));
    echo "Job " . $this->ProcessID . " done" . PHP_EOL;
  }
}

$Q = new ProcessQueue();
$Q->setMaxParallelProcesses(4);

foreach(range(1,10) as $Index){
  $Q->add(new MyJob());
}

while ($Q->count() > 0) {
  usleep(500000);
  echo "{$Q->countRunning()}/{$Q->count()} jobs running" . PHP_EOL;
}

?>