<?php

/**
 * Pngcrush adapter
 *
 * @see http://pmt.sourceforge.net/pngcrush/
 */
class isicsImageOptimPngcrushAdapter implements isicsImageOptimAdapter
{
  public function getToolName()
  {
    return 'Pngcrush';
  }
  
  public function optimize($file, $options = array())
  {
    exec('which pngcrush', $output, $return);
    
    if (!count($output) || $return > 0)
    {
      throw new RuntimeException('The Pngcrush program is not available nor accessible by php on your system');
    }

    $new_file = sprintf('%s.new', $file);
    exec(sprintf('pngcrush %s %s 2>/dev/null', escapeshellarg($file), escapeshellarg($new_file)), $output, $return);
    
    rename($new_file, $file);
  }
}