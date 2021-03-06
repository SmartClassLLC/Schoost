<?php

namespace SmartClass\Bundle\Schoost\DependencyInjection;

use SmartClass\Bundle\Schoost;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\ClassExistenceResource;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Security\Core\Authorization\ExpressionLanguage as SecurityExpressionLanguage;

class SchoostExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
    }
}
