<?php

namespace MaintenanceToolboxBundle\Tests\Form;

use MaintenanceToolboxBundle\Form\EditConfig;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactory;

class EditConfigTest extends TestCase
{
    public function testCanGetDefaultValues()
    {
        $formFactory = $this->createMock(FormFactory::class);
        $editForm = new EditConfig($formFactory);
        self::assertIsArray($editForm->getDefaultValues());
    }
}
