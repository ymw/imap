<?php

/*
 * This file is part of the YmwImap package.
 *
 * (c) Mohamed Ben Henda <benhenda.med@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace YMW\ImapBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;


class YmwImap extends ConfigurableExtension
{
	/**
	 * {@inheritdoc}
	 */
	public function loadInternal(array $configs, ContainerBuilder $container)
	{
		$configuration = new Configuration();
		$config = $this->processConfiguration($configuration, $configs);

		$container->setParameter('imap', $config['imap']);
		$container->setParameter('access', $config['access']);
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
		$loader->load('services.yml');
	}
}
