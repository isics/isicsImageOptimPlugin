<?php

class isicsImageOptimService
{
  const
    FORMAT_JPEG = 0,
    FORMAT_PNG  = 1;
    
  protected $logInfos;
  
  public function __construct()
  {
    $this->logInfos = array('nb_files' => 0, 'total_gain' => 0, 'total_time' => 0, 'files' => array());
  }
  
  public function getLogInfos()
  {
    return $this->logInfos;
  }
    
  /**
   * Optimizes images using adapters
   *
   * @param string $file     Image path
   * @param array  $options  Options
   *
   * @author Nicolas Charlot <nicolas.charlot@isics.fr>
   */
  public function optimize($file, $options = null)
  {
    if (false !== strripos($file, '.jpg') || false !== strripos($file, '.jpeg'))
    {
      $format = self::FORMAT_JPEG;
    }
    else if (false !== strripos($file, '.png'))
    {
      $format = self::FORMAT_PNG;
    }
    else
    {
      throw new InvalidArgumentException('Only JPEG and PNG formats are supported.');
    }
    
    if (sfConfig::get('sf_logging_enabled'))
    {
      $log_entry_key = $file;      
      $log_entry     = array('initial_size' => filesize($file), 'time' => microtime('true'));
    }    
    
    if (isset($options['adapters']))
    {
      $adapters = $options['adapters'];
    }
    else
    {
      $adapters = sfConfig::get(
        self::FORMAT_JPEG == $format ? 'app_isics_image_optim_plugin_jpeg_adapters' : 'app_isics_image_optim_plugin_png_adapters',
        array()
      );
    }
    
    if (isset($options['dest']))
    {
      copy($file, $options['dest']);
      $file = $options['dest'];
    }
    
    $default_adapters_options = sfConfig::get('app_isics_image_optim_plugin_adpters_options', array());
    
    if (isset($options['nb_pass']))
    {
      $nb_pass = $options['nb_pass'];
    }
    else
    {
      $nb_pass = sfConfig::get(self::FORMAT_JPEG == $format ? 'app_isics_image_optim_plugin_jpeg_nb_pass' : 'app_isics_image_optim_plugin_png_nb_pass', 1);
    }
    
    $pass = 1;
    while ($pass <= $nb_pass)
    {
      if (sfConfig::get('sf_logging_enabled'))
      {
        $log_entry['pass'][$pass] = array();
      }
      
      foreach ($adapters as $adapter)
      {
        if (sfConfig::get('sf_logging_enabled'))
        {
          $timer_start = microtime(true);
        }        
        
        if (isset($options['adapters_options'][$adapter]))
        {
          $adapter_options = $options['adapters_options'][$adapter];
        }
        else
        {
          $adapter_options = isset($default_adapters_options[$adapter]) ? $default_adapters_options[$adapter] : array();
        }

        $tmp_file = sprintf('%s.tmp', $file);

        copy($file, $tmp_file);

        $adapter_instance = new $adapter();
        $adapter_instance->optimize($tmp_file, $adapter_options);

        $file_size     = filesize($file);
        $tmp_file_size = filesize($tmp_file);

        if (file_exists($tmp_file) && $tmp_file_size < $file_size)
        {
          copy($tmp_file, $file);
        }
        
        if (sfConfig::get('sf_logging_enabled'))
        {
          $log_entry['pass'][$pass][$adapter_instance->getToolName()] = array(
            'size'        => $tmp_file_size,
            'won_size'    => $won_size = round($file_size-$tmp_file_size, 1),
            'won_percent' => round($won_size/$log_entry['initial_size']*100, 1),
            'time'        => microtime(true) - $timer_start
          );
        }

        unlink($tmp_file);
      }
      
      $pass++; 
    }
    
    if (sfConfig::get('sf_logging_enabled'))
    {
      $log_entry['final_size']  = filesize($file);
      $log_entry['won_size']    = $log_entry['initial_size']-$log_entry['final_size'];
      $log_entry['won_percent'] = round($log_entry['won_size']/$log_entry['initial_size']*100, 1);
      $log_entry['time']        = microtime(true) - $log_entry['time'];
      
      $this->logInfos['files'][$log_entry_key] = $log_entry;
      $this->logInfos['nb_files']              = $this->logInfos['nb_files'] + 1;
      $this->logInfos['total_gain']            = $this->logInfos['total_gain'] + $log_entry['won_size'];
    }
  }
}