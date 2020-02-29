<?php

namespace SmartClass\Bundle\Schoost;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SchoostBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddParamConverterPass());
        $container->addCompilerPass(new OptimizerPass());
        $container->addCompilerPass(new AddExpressionLanguageProvidersPass());
    }
}
