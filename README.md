# itCrossAppRouting Plugin

## Requirements
* sfDependencyInjectionPlugin

## Installation

## copy and activate
Copy the plugin to your projects plugins folder.
You have to activate the Plugin in your global ProjectConfiguration.
    class ProjectConfiguration extends sfProjectConfiguration { 
        ...
        public function setup()	{
            ...
            $this->enablePlugins(array(
                ...
                'itCrossAppRoutingPlugin',
                ...
                //notice that the sfDependencyInjectionPlugin has to be enabled AFTER the itCrossAppRoutingPlguin
                'sfDependencyInjectionPlugin',
            );
        }
    }

### register helper
The helper itCrossAppRoutingHelper can also be globally enabeld.
This will be done by defining it as standard_helper in settings.yml @see http://www.symfony-project.org/reference/1_4/en/04-Settings#chapter_04_sub_standard_helpers 

### set app_routing.yml
The Plugin entroduces a new configurationfile 'app_routing.yml'.
This is an Environment specific yml Config like the most symfony config files.

The following settings are supported
Example:
    ../config/app_routing.yml
    all:
      # key is the application name
      # optionally you can use the :sf_culture placeholder to inject the cluture in path prefix
      online_guard: 
        domain: http://ifb.berlinale.de
        route_prefix: :domain/:sf_culture/path/to/online_guard
      online_boa:
        domain: http://ifb.berlinale.de
        route_prefix: :domain/:sf_culture/path/to/online_boa

## Usage
The Routing can be called due to the configured service 'app_routing'

### Example Actions / Components
    ../actions.class.php
    public function executeMyAction()	{
        $this->getService('app_routing')
            ->generateApplicationUrl(
               'application_name', 
               'route_name', 
               array('param'=>'123test',
               $absolute)
        );
        ...
    }

### Example anywhere in the symfony context (eg. filter, ..)

    public function getAnApplicationLink(){
      $appRouting = sfContext::getInstance()->getConfiguration()->getService('app_routing');
      return $appRouting->generateApplicationUrl(
               'application_name', 
               'route_name', 
               array('param'=>'123test', $absolute);
    }

### Example template
    ../myActionSuccess.php
    //include helper if not already defined in standard_helpers
    use_helper('itCrossAppRouting');

    echo it_cross_app_url_for('app_name', 'route_name', array('param'=>'test123'));

### Usage via string notation

for using in routing.yml there is an altenative syntax to descripe app routings
the convention is like

    $alternativeNotation = '@@appname?app_param=value@routename?routeparam=othervalue'

so you can call

    $appRouting->generateApplicationUrlByString($alternativeNotation);

    
    

