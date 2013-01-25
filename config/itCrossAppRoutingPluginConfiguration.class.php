<?php
class itCrossAppRoutingPluginConfiguration extends sfPluginConfiguration	{

  public function initialize() {
    $this->initAppRoutingConfig();
    $this->dispatcher->connect('service_container.load_configuration', array($this, 'listenToServiceContainerLoadConfiguration'));
  }

  public function listenToServiceContainerLoadConfiguration(sfEvent $event) {
    $container = $event->getSubject();
    $loader    = new sfServiceContainerLoaderFileYaml($container);
    $loader->load(dirname(__FILE__).'/services.yml');
  }

  /**
   * inits application route config app_routing.yml
   */
  protected function initAppRoutingConfig()  {
     
    $config = 'config/app_routing.yml';
    if($this->configuration instanceof sfApplicationConfiguration)  {
      $cacheConfig =  new sfConfigCache($this->configuration);
      $cacheConfig->import($config, true);
    }
    else {
      $configFiles = $this->configuration->getConfigPaths($config);
      sfConfig::set(itCrossAppConfigHandler::DEFAULT_PREFIX, itCrossAppConfigHandler::getConfiguration($configFiles));
    }
  }
}