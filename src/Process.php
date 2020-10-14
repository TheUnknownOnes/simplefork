<?php

namespace TUO\SimpleFork;

abstract class Process {
  protected $Running = false;
  protected $Started = false;
  protected $ProcessID = null;
  protected $TermSignale = null;
  protected $StopSignal = null;
  protected $ErrorCode = null;
  protected $ErrorMessage = null;
  protected $Signaled = false;

  public function start() {
    $this->update();

    if ($this->Running) {
      return true;
    }

    $PID = pcntl_fork();

    if ($PID < 0) {
      throw new RuntimeException("Error while forking: " . pcntl_strerror(pcntl_get_last_error()));
    }
    elseif ($PID > 0) {
      $this->ProcessID = $PID;
      $this->Running = true;
      $this->Started = true;
      $this->TermSignal = null;
      $this->StopSignal = null;
      $this->ErrorCode = null;
      $this->ErrorMessage = null;
      $this->Signaled = false;
      return true;
    }
    else {
      $this->ProcessID = getmypid();
      $this->run();
      exit(0);
    }
  }

  public function update() {
    if (! $this->Running)
      return;

    $ChildResult = pcntl_waitpid($this->ProcessID, $Status, WNOHANG | WUNTRACED);

    if ($ChildResult === -1) {
      throw new RuntimeException(pcntl_strerror(pcntl_get_last_error()));
    }
    elseif ($ChildResult === 0) {
      $this->Running = true;
    }
    else {
      $this->Signaled = false;

      if (pcntl_wifsignaled($Status)) {
        $this->Signaled = true;
        $this->TermSignal = pcntl_wtermsig($Status);
      }

      if (pcntl_wifstopped($Status)) {
        $this->Signaled = true;
        $this->StopSignal = pcntl_wstopsig($Status);
      }

      if (pcntl_wifexited($Status)) {
        $this->ErrorCode = pcntl_wexitstatus($Status);
        $this->ErrorMessage = pcntl_strerror($this->ErrorCode);
      }

      $this->Running = false;
    }
  }

  public function kill($Signal) {
    $this->update();
    if (! $this->Running)
      return true;

    return posix_kill($this->ProcessID, $Signal);
  }

  public function wait($Sleeptime = 100000) {
    while ($this->isRunning()) {
      usleep($Sleeptime);
    }
  }

  public function isRunning() {
    $this->update();
    return $this->Running;
  }

  public function wasStarted() {
    return $this->Started;
  }

  public function wasSignaled() {
    $this->update();
    return $this->Signaled;
  }

  public function getErrorCode() {
    $this->update();
    return $this->ErrorCode;
  }

  public function getErrorMessage() {
    $this->update();
    return $this->getErrorMessage;
  }

  //override this to define the work to be done
  abstract public function run();
}

?>