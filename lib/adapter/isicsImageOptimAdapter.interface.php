<?php

interface isicsImageOptimAdapter
{
  public function getToolName();
  
  public function optimize($file, $options);
}