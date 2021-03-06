<?php

declare(strict_types=1);

namespace AdminPanel\Component\DataSource;

/**
 * {@inheritdoc}
 */
abstract class DataSourceAbstractExtension implements DataSourceExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadSubscribers()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function loadDriverExtensions()
    {
        return [];
    }
}
