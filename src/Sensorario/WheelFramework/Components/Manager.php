<?php

namespace Sensorario\WheelFramework\Components;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Manager
{
    private $entityManager;

    private $config;

    public function setConfiguration(Config $config)
    {
        $this->config = $config;
    }

    public function init()
    {
        $doctrine = $this->config
            ->getConfig('doctrine');

        $config = Setup::createAnnotationMetadataConfiguration(
            $doctrine['path'],
            false,
            null,
            null,
            false
        );

        $this->entityManager = EntityManager::create(
            $doctrine['db_params'],
            $config
        );
    }

    public function getRepository($repository)
    {
        return $this->entityManager->getRepository($repository);
    }

    public function persist($entity)
    {
        $this->entityManager->persist($entity);
    }

    public function flush()
    {
        $this->entityManager->flush();
    }

    public function remove($entity)
    {
        $this->entityManager->remove($entity);
    }
}
