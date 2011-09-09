<?php

/**
 * Pngcrush adapter
 *
 * @see http://optipng.sourceforge.net/
 */
class isicsImageOptimOptiPNGAdapter implements isicsImageOptimAdapter
{
  public function getToolName()
  {
    return 'OptiPNG';
  }
  
  public function optimize($file, $options = array())
  {
    exec('which pngcrush', $output, $return);
    
    if (!count($output) || $return > 0)
    {
      throw new RuntimeException('The OptiPNG program is not available nor accessible by php on your system');
    }
    
    if (!isset($options['level']))
    {
      $options['level'] = 2;
    }

    exec(sprintf('optipng -o %s %s 2>/dev/null', $options['level'], escapeshellarg($file)), $output, $return);
  }
}