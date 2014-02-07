<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FSi\Bundle\AdminBundle\Admin\Doctrine\Context\Request;

use FSi\Bundle\AdminBundle\Event\CRUDEvents;

class CreateFormValidRequestHandler extends AbstractFormValidRequestHandler
{
    /**
     * @return string
     */
    protected function getEntityPreSaveEventName()
    {
        return CRUDEvents::CRUD_CREATE_ENTITY_PRE_SAVE;
    }

    /**
     * @return string
     */
    protected function getEntityPostSaveEventName()
    {
        return CRUDEvents::CRUD_CREATE_ENTITY_POST_SAVE;
    }

    /**
     * @return string
     */
    protected function getResponsePreRenderEventName()
    {
        return CRUDEvents::CRUD_CREATE_RESPONSE_PRE_RENDER;
    }
}