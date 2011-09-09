<?php

/**
 * AdvPNG adapter
 *
 * @see http://advancemame.sourceforge.net/comp-readme.html
 */
class isicsImageOptimAdvPNGAdapter implements isicsImageOptimAdapter
{
  public function getToolName()
  {
    return 'AdvPNG';
  }
  
  public function optimize($file, $options = array())
  {
    exec('which advpng', $output, $return);
    
    if (!count($output) || $return > 0)
    {
      throw new RuntimeException('The AdvPNG program is not available nor accessible by php on your system');
    }

    exec(sprintf('advpng -4 -z %s 2>/dev/null', escapeshellarg($file)), $output, $return);
  }
}