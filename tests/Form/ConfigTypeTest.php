<?php

namespace MaintenanceToolboxBundle\Tests\Form;

use MaintenanceToolboxBundle\Form\ConfigType;
use Symfony\Component\Form\Test\TypeTestCase;

class ConfigTypeTest extends TypeTestCase
{
    /** @var array */
    private $formData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formData = [
            'release__enabled' => true,
        ];
    }

    public function testSubmitValidData()
    {
        $form = $this->factory->create(ConfigType::class);
        $form->submit($this->formData);

        self::assertTrue($form->isSynchronized());
    }

    public function testDefaultValuesCanBeSet()
    {
        $view = $this->factory->create(ConfigType::class, $this->formData)->createView();
        self::assertArrayHasKey('data', $view->vars);
        self::assertSame($this->formData, $view->vars['data']);
    }
}
