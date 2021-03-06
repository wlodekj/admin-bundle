<?php

declare(strict_types=1);

namespace AdminPanel\Symfony\AdminBundle\DataGrid\Extension\Admin\ColumnTypeExtension;

use AdminPanel\Symfony\AdminBundle\Admin\Manager;
use AdminPanel\Symfony\AdminBundle\Exception\RuntimeException;
use AdminPanel\Symfony\AdminBundle\DataGrid\Extension\Symfony\ColumnType\Action;
use AdminPanel\Component\DataGrid\Column\ColumnAbstractTypeExtension;
use AdminPanel\Component\DataGrid\Column\ColumnTypeInterface;

class ElementActionExtension extends ColumnAbstractTypeExtension
{
    /**
     * @var \AdminPanel\Symfony\AdminBundle\Admin\Manager
     */
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @inheritdoc
     */
    public function getExtendedColumnTypes()
    {
        return ['action'];
    }

    /**
     * @inheritdoc
     */
    public function initOptions(ColumnTypeInterface $column)
    {
        $this->validateColumn($column);

        $column->getActionOptionsResolver()->setDefined(['element']);
        $column->getActionOptionsResolver()->setAllowedTypes('element', 'string');
    }

    public function filterValue(ColumnTypeInterface $column, $value)
    {
        $this->validateColumn($column);

        $actions = $column->getOption('actions');
        $generatedActions = [];
        foreach ($actions as $action => $actionOptions) {
            if (!$this->validateActionOptions($column, $action, $actionOptions)) {
                continue;
            }

            $generatedActions[$action] = $this->generateActionOptions($actionOptions);
            unset($actions[$action]['element']);
        }

        $column->setOption('actions', array_replace_recursive($generatedActions, $actions));

        return parent::filterValue($column, $value);
    }

    /**
     * @param \AdminPanel\Component\DataGrid\Column\ColumnTypeInterface $column
     */
    private function validateColumn(ColumnTypeInterface $column)
    {
        if (!($column instanceof Action)) {
            throw new RuntimeException(sprintf(
                '%s can extend only %s, but got %s',
                get_class($this),
                Action::class,
                get_class($column)
            ));
        }
    }

    /**
     * @param \AdminPanel\Component\DataGrid\Column\ColumnTypeInterface $column
     * @param string $action
     * @param array $actionOptions
     */
    private function validateActionOptions(ColumnTypeInterface $column, $action, array $actionOptions)
    {
        if (!isset($actionOptions['element'])) {
            return false;
        }

        if (!$this->manager->hasElement($actionOptions['element'])) {
            throw new RuntimeException(sprintf(
                'Unknown element "%s" specified in action "%s" of datagrid "%s"',
                $actionOptions['element'],
                $action,
                $column->getDataGrid()->getName()
            ));
        }

        return true;
    }

    /**
     * @param array $actionOptions
     * @return array
     */
    private function generateActionOptions(array $actionOptions)
    {
        $element = $this->manager->getElement($actionOptions['element']);

        $additionalParameters = array_merge(
            ['element' => $element->getId()],
            $element->getRouteParameters(),
            isset($actionOptions['additional_parameters']) ? $actionOptions['additional_parameters'] : []
        );

        return [
            'route_name' => $element->getRoute(),
            'additional_parameters' => $additionalParameters,
            'parameters_field_mapping' => ['id' => 'id']
        ];
    }
}
