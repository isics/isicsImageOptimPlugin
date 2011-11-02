<?php

/**
 * Jpegtran adapter
 *
 * @see http://jpegclub.org/
 */
class isicsImageOptimJpegtranAdapter implements isicsImageOptimAdapter
{
  public function getToolName()
  {
    return 'Jpegtran';
  }
  
  public function optimize($file, $options = array())
  {
    exec('which jpegtran', $output, $return);
    
    if (!count($output) || $return > 0)
    {
      throw new RuntimeException('The Jpegtran program is not available nor accessible by php on your system');
    }

    $new_file = sprintf('%s.new', $file);
    exec(sprintf('jpegtran -copy none -optimize -perfect -progressive -outfile %s %s 2>/dev/null', escapeshellarg($new_file), escapeshellarg($file)), $output, $return);

    rename($new_file, $file);
  }
}