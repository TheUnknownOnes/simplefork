<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TUO\SimpleFork\AdvancedProcess;

class MyJob extends AdvancedProcess {
  protected $Stopped = false;

  public function run() {
    $cnt = 1;
    while ($cnt++ < 5) {
      if ($this->Stopped)
        break;
      sleep(1);
    }
    return 0;
  }

  protected function OnSignalTerminate() {
    $this->Stopped = true;
  }
}

$Job = new MyJob();

$Job->start();

sleep(1);

$Job->kill(SIGTERM);
$Job->wait();

echo "Job exited with code {$Job->getErrorCode()}" . PHP_EOL;

?>