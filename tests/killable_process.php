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
    echo "[Child] Exiting" . PHP_EOL;
    return 1;
  }

  public function OnSignalTerminate() {
    echo "[Child] Got signal to terminate" . PHP_EOL;
    $this->Stopped = true;
  }
}

$Job = new MyJob();

$Job->start();

sleep(1);

echo "[Parent] Sending SIGTERM" . PHP_EOL;
$Job->kill(SIGTERM);
echo "[Parent] Waiting for job" . PHP_EOL;
$Job->wait();

echo "[Parent] Job exited with code {$Job->getErrorCode()}" . PHP_EOL;

?>