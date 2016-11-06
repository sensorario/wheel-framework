<?php

namespace Sensorario\WheelFramework\Components;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class Manager
{
    private $doctrineEntityManager;

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

        $this->doctrineEntityManager = EntityManager::create(
            $doctrine['db_params'],
            $config
        );
    }

    public function getDoctrineEntityManager()
    {
        return $this->doctrineEntityManager;
    }

    public function getRepository($repository)
    {
        if (!$this->doctrineEntityManager) {
            throw new \RuntimeException(
                'Entity Manager is not defined!'
            );
        }

        return $this->doctrineEntityManager->getRepository($repository);
    }

    public function persist($entity)
    {
        $this->doctrineEntityManager->persist($entity);
    }

    public function flush()
    {
        $this->doctrineEntityManager->flush();
    }

    public function remove($entity)
    {
        $this->doctrineEntityManager->remove($entity);
    }
}
