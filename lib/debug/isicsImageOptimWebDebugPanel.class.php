<?php

class isicsImageOptimWebDebugPanel extends sfWebDebugPanel
{
  /**
   * @see sfWebDebugPanel
   */
  public function getTitle()
  { 
    $log_infos = $this->getLogInfos();
    
    return sprintf(
      '<img src="/isicsImageOptimPlugin/images/debug.png" alt="isicsImageOptim" height="16" width="16" /> %s file(s) / %s KB',
      $log_infos['nb_files'],
      (0 < $log_infos['total_gain']) ? -round($log_infos['total_gain']/1000, 1) : 0
    );
  }

  /**
   * @see sfWebDebugPanel
   */
  public function getPanelTitle()
  {
    return 'isicsImageOptim';
  }

  /**
   * Shows information related to which files are currently being optimized
   *
   * @see sfWebDebugPanel
   */
  public function getPanelContent()
  {
    $log_infos = $this->getLogInfos();
    
    $to_return = '';

    foreach ($log_infos['files'] as $file => $infos)
    {
      $to_return .= sprintf('
        <h2>%s</h2>
        <table>
          <thead><tr><th>Pass</th><th>Adapter</th><th>Size</th><th>Gain</th><th>%%</th><th>Time</th></tr></thead>
          <tbody>
            <tr><th>INITIAL</th><td style="text-align: center">-</td><td style="text-align: right">%s KB</td><td style="text-align: center">-</td><td style="text-align: center">-</td><td style="text-align: center">-</td></tr>
        ',
        $file,
        round($infos['initial_size']/1000, 1)
      );

      foreach ($infos['pass'] as $pass_nb => $pass_infos)
      {
        foreach ($pass_infos as $adapter => $adapter_infos)
        {
          $to_return .= sprintf('
            <tr><th>#%s</th><td>%s</td><td style="text-align: right">%s KB</td><td style="text-align: right">%s KB</td><td style="text-align: right">%s %%</td><td style="text-align: right">%s ms</td></tr>',
            $pass_nb,
            $adapter,
            round($adapter_infos['size']/1000, 1),
            round($adapter_infos['won_size']/1000, 1),
            $adapter_infos['won_percent'],
            round($adapter_infos['time']*1000)
          );          
        }
      }
      
      $to_return .= sprintf('
            <tr><th>FINAL</th><td style="text-align: center">-</td><td style="text-align: right">%s KB</td><td style="text-align: right">%s KB</td><td style="text-align: right">%s %%</td><td style="text-align: right">%s ms</td></tr>
          </tbody>
        </table>',
        round($infos['final_size']/1000, 1),
        round($infos['won_size']/1000, 1),
        $infos['won_percent'],
        round($infos['time']*1000)
      );
    }
    
    return $to_return;
  }
  
  protected function getLogInfos()
  {
    return sfContext::getInstance()->get('isics_image_optim')->getLogInfos();
  }
}
