<?php

class isicsImageOptimPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    $this->dispatcher->connect('context.load_factories', array($this, 'listenToContextLoadFactoriesEvent'));    
    $this->dispatcher->connect('debug.web.load_panels', array($this, 'listenToLoadDebugWebPanelEvent'));
  }
  
  /**
   * Listens to the "context.load_factories" event to add a "isics_image_optim"
   * service to the current context object instance.
   *
   * @param sfEvent $event An event with an sfContext instance as the subject
   */
  public function listenToContextLoadFactoriesEvent(sfEvent $event)
  {
    $context = $event->getSubject();

    if (!class_exists($service_class = sfConfig::get('app_isics_image_optim_plugin_class', 'isicsImageOptimService')))
    {
      throw new sfConfigurationException(sprintf('The %s service class does not exist', $service_class));
    }

    $context->set('isics_image_optim', new $service_class());
  }  
  
  /**
   * Listens on the debug.web.load_panels event and adds the web debug panel
   *
   * @param sfEvent $event The event object for the debug.web.load_panel event
   */
  public function listenToLoadDebugWebPanelEvent(sfEvent $event)
  {
    $event->getSubject()->setPanel('isics_image_optim', new isicsImageOptimWebDebugPanel($event->getSubject()));
  }
}
