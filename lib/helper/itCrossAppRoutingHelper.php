<?php

/**
 *
 * generates a url for given app route
 * @param string $app_name
 * @param string $route_name
 * @param array $params
 * @return string absolute url to application route
 */
function it_cross_app_url_for($app_name, $route_name, $params = array(), $absolute = false, $prefixParams = array())
{
    return sfContext::getInstance()
        ->getConfiguration()
        ->getService('app_routing')
        ->generateApplicationUrl(
        $app_name,
        $route_name,
        $params,
        $absolute,
        $prefixParams
    );
}

/**
 *
 * generates url based on mofified uri string
 * @param string $uridef in form @@appname?prefixparam1=value&prefixparam2=...@routename?routeparam1=value&routeparam2=value
 * @return string
 */
function it_cross_app_url_for2($uridef, $absolute = false)
{
    if (preg_match('(^\w?://)', $uridef)) {
        return $uridef;
    }

    return sfContext::getInstance()
        ->getConfiguration()
        ->getService('app_routing')
        ->generateApplicationUrlByString($uridef, $absolute);
}