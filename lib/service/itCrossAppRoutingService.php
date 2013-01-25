<?php
class itCrossAppRoutingService
{


    protected $appRoutings = array();

    /**
     *
     * generates url for application based route
     * @param string $app_name
     * @param string $route_name
     * @param array $parameters
     * @param boolean $absolute (default is false)
     * @param array $prefixParams
     * @throws sfConfigurationException
     * @return string
     */
    public function generateApplicationUrl($app_name, $route_name, $parameters = array(), $absolute = false, $prefixParams = array())
    {
        $context = sfContext::getInstance();

        $name = 'sf_app_routing_' . $app_name;
        $config = sfConfig::get('sf_app_routing');
        if (!$config[$app_name]) {
            throw new sfConfigurationException('no application routing defined for app ' . $app_name);
        }
        //add some system params for replacement in prefix
        $routeDomain = '';
        $routeDudenDomain = '';
        if ($absolute) {
            if (!empty($config['domain'])) {
                $routeDomain = $config['domain'];
            }
            if (!empty($config[$app_name]['domain'])) {
                $routeDomain = $config[$app_name]['domain'];
            }
            if (!empty($config['duden_domain'])) {
                $routeDudenDomain = $config['duden_domain'];
            }
            if (!empty($config[$app_name]['duden_domain'])) {
                $routeDudenDomain = $config[$app_name]['duden_domain'];
            }
        }
        $replacement = array(
            'sf_culture' => isset($prefixParams['sf_culture']) ? $prefixParams['sf_culture'] : $context->getUser()->getCulture(),
            'domain' => isset($prefixParams['domain']) ? $prefixParams['domain'] : $routeDomain,
            'duden_domain' => isset($prefixParams['duden_domain']) ? $prefixParams['duden_domain'] : $routeDudenDomain,
        );
        foreach ($prefixParams as $param => $value) {
            $replacement[$param] = $value;
        }
        $routePrefix = $config[$app_name]['route_prefix'];

        foreach ($replacement as $paramName => $paramValue) {
            $routePrefix = preg_replace('@:' . $paramName . '@', $paramValue, $routePrefix);
        }

        $scriptName = (sfConfig::get('sf_no_script_name', false) ? '' : '/'.basename($context->getRequest()->getScriptName()));

        return rtrim($routePrefix, '/') . $scriptName . $this->getApplicationRouting($app_name)->generate($route_name, $parameters);
    }

    /**
     *
     * @param string $uridef in form @@appname?prefixparam1=value&prefixparam2=...@routename?routeparam1=value&routeparam2=value
     * @param boolean indicating whether should generate absolute url
     * @return string
     *
     */
    public function generateApplicationUrlByString($uri_string, $absolute = false)
    {
        $isAppRoute = substr($uri_string, 0, 2) === '@@';

        $config = sfContext::getInstance()->getConfiguration();
        if (!$isAppRoute) {
            throw new Exception('given route is no app route');
        }

        /* @var $app_routing itCrossAppRoutingService */
        $matches;
        preg_match('%@@(\w+)\??(.*)@(\w+)\??(.*)$%', $uri_string, $matches);
        $appName = $matches[1];
        parse_str($matches[2], $prefixParams);
        $routeName = $matches[3];
        parse_str($matches[4], $routeParams);

        // replace :values with values from current request context
        $tmpParams = $routeParams;
        $request = sfContext::getInstance()->getRequest();
        foreach ($tmpParams as $name => $value) {
            if (strlen($value && $value[0] === ':')) {
                $refParamName = substr($value, 1);
                $routeParams[$name] = $request->getParameter($refParamName);
            }
        }
        return $this->generateApplicationUrl($appName, $routeName, $routeParams, $absolute, $prefixParams);
    }

    /**
     *
     * gets the routing class for application given
     * @param string $app_name
     */
    protected function getApplicationRouting($app_name)
    {
        if (!isset($this->appRoutings[$app_name])) {
            $routing = new sfPatternRouting(new sfEventDispatcher());
            $config = new sfRoutingConfigHandler();
            $routingPath = array(sfConfig::get('sf_apps_dir'),
                $app_name,
                'config',
                'routing.yml'
            );
            $routingPath = implode(DIRECTORY_SEPARATOR, $routingPath);
            $routing->setRoutes($config->evaluate(array($routingPath)));
            $this->appRoutings[$app_name] = $routing;
        }
        return $this->appRoutings[$app_name];
    }

}