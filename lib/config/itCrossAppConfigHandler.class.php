<?php

class itCrossAppConfigHandler extends sfYamlConfigHandler	{

  const DEFAULT_PREFIX = 'sf_app_routing';

  public function execute($configFiles)
  {

    $prefix = strtolower($this->getParameterHolder()->get('prefix', self::DEFAULT_PREFIX));
    $config = self::getConfiguration($configFiles);

    // compile data
    $retval = sprintf("<?php\n".
                      "// auto-generated by %s\n".
                      "// date: %s\nsfConfig::set('{$prefix}', \n%s\n);\n?>",
    __CLASS__, date('Y/m/d H:i:s'), var_export($config,true));

    return $retval;
  }

  /**
   * @see sfConfigHandler
   */
  static public function getConfiguration(array $configFiles)
  {
    return self::replaceConstants(self::flattenConfigurationWithEnvironment(self::parseYamls($configFiles)));
  }

}
