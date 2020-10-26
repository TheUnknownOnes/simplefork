<?php

namespace TUO\SimpleFork;

abstract class AdvancedProcess extends Process {

  protected function OnBeforeRun() {
    parent::OnBeforeRun();

    pcntl_async_signals(true);

    pcntl_signal(SIGTERM, [$this, 'OnSignalTerminate']);
    pcntl_signal(SIGHUP, [$this, 'OnSignalHangup']);
  }

  public function OnSignalHangup() {
    //override this in your class
  }

  public function OnSignalTerminate() {
    //override this in your class
  }
}

?>