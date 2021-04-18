# PHP-based Symfony service configuration

![Build status](https://github.com/kostislav/php-class-symfony-service-config/actions/workflows/php.yml/badge.svg)

The Symfony framework offers three methods of configuring its [Dependency Injection Container](https://symfony.com/doc/current/service_container.html#explicitly-configuring-services-and-arguments): XML configuration, YAML configuration and PHP configuration.
The XML and YAML approaches are a little developer-unfriendly because of things like missing IDE completion. The PHP approach tries to solve these problems, but instead of using PHP directly, it invents a DSL on top of PHP and in turn suffers from the same problems, albeit to a lesser degree.

This library offers a different method of configuring services using plain PHP, heavily inspired by [Java's Spring framework Java-based configuration](https://docs.spring.io/spring-framework/docs/current/reference/html/core.html#beans-java).

**NOTE:** The library makes extensive use of [PHP attributes](https://www.php.net/manual/en/language.attributes.overview.php), which means it requires at least PHP 8.0.

## Example

Consider the following simple setup: One public service depending on two other services. In Symfony PHP config, it might look like this:
```php
return function(ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set('innerService1', SimpleService::class)
        ->arg(['Hello']);

    $services->set('innerService2', SimpleService::class)
        ->args(['world!']);

    $services->set('combinedService', CombinedService::class)
        ->args(service('innerService1'), service('innerService2'))
        ->public();
}
```
Or, if you prefer YAML:
```yaml
service1:
  class: SimpleService
  arguments: [ 'Hello' ]

service2:
  class: SimpleService
  arguments: [ 'world!' ]

combinedService:
  class: CombinedService
  public: true
  arguments: [ '@service1', '@service2' ]
```

Using this library, you can write the same definition like this:
```php
class ExampleConfig {
    #[ServiceDefinition]
    public function innerService1(): SimpleService {
        return new SimpleService('Hello');
    }

    #[ServiceDefinition]
    public function innerService2(): SimpleService {
        return new SimpleService('world!');
    }

    #[ServiceDefinition(isPublic: true)]
    public function combinedService(SimpleService $innerService1, SimpleService $innerService2): CombinedService {
        return new CombinedService($innerService1, $innerService2);
    }
}
```
For this simple example, the code might not be shorter, but there is an obvious advantage: the creation of your services is written in plain PHP. IDE features like code completion, type hinting and usage analysis work out of the box.
And if you need to do something more complicated, like calling a setter or using a static factory method, you can do it like in any other place in your codebase - no need to learn yet another [expression language](https://symfony.com/doc/current/service_container/expression_language.html) to call a method on another service.

## Setting it up

1. Add a composer dependency on `kostislav/php-class-symfony-service-config`
1. Unfortunately, Symfony does not offer any way for the library to hook into the right internals, so a manual change of your `Kernel` class is necessary. Add the following line to the `configureContainer` method: 
```php
protected function configureContainer(ContainerBuilder $container, Loader $loader): void {
    $loader->getResolver()->addLoader(new Kostislav\ClassConfig\ConfigClassServiceConfigLoader($container));
    // other stuff
}
```
3. Register your configuration classes with the `$loader`. A convenient place to do it is in the `Kernel` class, just below the line added in step 2.
```php
protected function configureContainer(ContainerBuilder $container, Loader $loader): void {
    $loader->getResolver()->addLoader(new Kostislav\ClassConfig\ConfigClassServiceConfigLoader($container));
    // now you can load all your config classes with $loader
    $loader->load(MyConfig::class);
}
```

## What it does

By default, each public non-static method in the config class that is annotated with the `ServiceDefinition` attribute will be used to create a private service with the same name as that method. This method **must** have a return type hint. Any parameters of this method will be added as dependencies of this service and will be resolved by name.

The config class itself must have a no-argument constructor.


### Explicitly naming a service

If you need a service to have a name other than the defining method, pass it to the `ServiceDefinition` attribute.
```php
#[ServiceDefinition('alternative.name')]
public function whatever(): SimpleService {
    return new SimpleService('serv1');
}
```

### Public services
In Symfony, services are by default private and cannot be requested from the container. Things like controllers need to be marked as public. This is controlled by the `isPublic` parameter of the `ServiceDefinition` attribute.
```php
#[ServiceDefinition(isPublic: true)]
public function publicService(): SimpleService {
    return new SimpleService('serv1');
}
```

### Using a service with a name that cannot be used as a PHP identifier

Symfony is full of services with names like `annotations.cached_reader`. As PHP won't allow us to use parameter names like that, you can specify the name of the injected service explicitly using the `Service` parameter attribute.
```php
#[ServiceDefinition]
public function myService(#[Service('annotations.cached_reader')] Reader annotationReader): SimpleService {
    // do whatever here
}
```

### Using container parameters
Method parameters annotated with the `Parameter` attribute will be populated with the corresponding container parameter rather than a service. Again, by default, the method parameter name is used as the name of the parameter to look up, but another name can be specified on the attribute.
```php
#[ServiceDefinition]
public function myService(#[Parameter('kernel.debug')] string $debug): SimpleService {
    // do whatever here
}
```

### Splitting the configuration class
When you have a lot of services, the configuration class can get long. You can split it into multiple classes and either load each of them in your `Kernel`, or just load one of them and use the `Import` attribute to include the other ones. Services from the imported classes will be available in the importing config class.

Imports work transitively - if the imported class itself has another `Import` attribute with another class, that class will be included as well. 
```php
#[Import(AnotherConfig::class)]
class OneConfig {
    // some service definitions here
}
```

### Service tags
If you need to tag your service, just add a `Tag` attribute. There can be multiple  `Tag` attributes on the same method.
```php
#[Tag('kernel.event_listener')]
#[ServiceDefinition]
public function myService(): SimpleService {
    // do whatever here
}
```
## Performance
What is the performance impact of all this reflection and attribute reading?
Don't worry, the configuration classes are only analyzed once when the container is built, at the same time the `services.yaml` file would get parsed. After that, the performance difference compared to the other approaches is one additional method call (of the service definition method), which isn't noticeable at all.