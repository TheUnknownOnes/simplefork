<?php

namespace TUO\SimpleFork;

class ProcessQueue {
  protected $Processes = [];
  protected $MaxParallelProcesses = 4;
  protected $RemoveFinishedProcesses = true;
  protected $RunningProcesses = 0;

  public function setMaxParallelProcesses(Int $Count) {
    if ($Count > 0)
      $this->MaxParallelProcesses = $Count;
    return $this;
  }

  public function getMaxParallelProcesses() {
    return $this->getMaxParallelProcesses();
  }

  public function setRemoveFinishedProcesses(Bool $Remove) {
    $this->RemoveFinishedProcesses = $Remove;
    return $this;
  }

  public function add(Process $Process) {
    $this->Processes[] = $Process;
    $this->update();

    return $this;
  }

  public function update() {
    $this->RunningProcesses = 0;

    foreach($this->Processes as $Index => $Process) {
      if (($Process->wasStarted()) && (! $Process->isRunning()) && ($this->RemoveFinishedProcesses)) {
        unset($this->Processes[$Index]);
      }

      if ($Process->isRunning())
        $this->RunningProcesses++;
    }

    $ProcessesToStart = $this->MaxParallelProcesses - $this->RunningProcesses;
    
    if ($ProcessesToStart > 0) {
      foreach($this->Processes as $Process) {
        if (! $Process->wasStarted()) {
          $Process->start();
          $ProcessesToStart--;
        }

        if ($ProcessesToStart < 1) {
          break;
        }
      }
    }

    return $this;
  }

  public function count() {
    $this->update();
    return count($this->Processes);
  }

  public function countRunning() {
    $this->update();
    return $this->RunningProcesses;
  }

  public function wait($Sleeptime = 100000) {
    while ($this->count() > 0) {
      usleep($Sleeptime);
    }

    return $this;
  }
}

?>