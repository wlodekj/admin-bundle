<?php

declare(strict_types=1);

namespace AdminPanel\Component\DataSource\Extension\Symfony\DependencyInjection\Driver;

use AdminPanel\Component\DataSource\Driver\DriverAbstractExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AdminPanel\Component\DataSource\Field\FieldTypeInterface;

/**
 * DependencyInjection extension loads various types of extensions from Symfony's service container.
 */
class DriverExtension extends DriverAbstractExtension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $driverType;

    /**
     * @var array
     */
    protected $fieldServiceIds;

    /**
     * @var array
     */
    protected $fieldExtensionServiceIds;

    /**
     * @var array
     */
    protected $subscriberServiceIds;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param string $driverType
     * @param array $fieldServiceIds
     * @param array $fieldExtensionServiceIds
     * @param array $subscriberServiceIds
     */
    public function __construct(ContainerInterface $container, $driverType, array $fieldServiceIds, array $fieldExtensionServiceIds, array $subscriberServiceIds)
    {
        $this->container = $container;
        $this->driverType = $driverType;
        $this->fieldServiceIds = $fieldServiceIds;
        $this->fieldExtensionServiceIds = $fieldExtensionServiceIds;
        $this->subscriberServiceIds = $subscriberServiceIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedDriverTypes() : array
    {
        return [$this->driverType];
    }

    /**
     * {@inheritdoc}
     */
    public function hasFieldType(string $type) : bool
    {
        return isset($this->fieldServiceIds[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldType(string $type) : FieldTypeInterface
    {
        if (!isset($this->fieldServiceIds[$type])) {
            throw new \InvalidArgumentException(sprintf('The field type "%s" is not registered within the service container.', $type));
        }

        $type = $this->container->get($this->fieldServiceIds[$type]);

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFieldTypeExtensions(string $type) : bool
    {
        foreach ($this->fieldExtensionServiceIds as $alias => $extensionName) {
            $extension = $this->container->get($this->fieldExtensionServiceIds[$alias]);
            $types = $extension->getExtendedFieldTypes();
            if (in_array($type, $types)) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldTypeExtensions(string $type)
    {
        $fieldExtension = [];

        foreach ($this->fieldExtensionServiceIds as $alias => $extensionName) {
            $extension = $this->container->get($this->fieldExtensionServiceIds[$alias]);
            $types = $extension->getExtendedFieldTypes();
            if (in_array($type, $types)) {
                $fieldExtension[] = $extension;
            }
        }

        return $fieldExtension;
    }

    /**
     * {@inheritdoc}
     */
    public function loadSubscribers() : array
    {
        $subscribers = [];

        foreach ($this->subscriberServiceIds as $alias => $subscriberName) {
            $subscriber = $this->container->get($this->subscriberServiceIds[$alias]);
            $subscribers[] = $subscriber;
        }

        return $subscribers;
    }
}
