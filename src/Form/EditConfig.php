<?php

namespace MaintenanceToolboxBundle\Form;

use MaintenanceToolboxBundle\Tool\ArrayFormatter;
use MaintenanceToolboxBundle\Config\ToolboxConfig;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class EditConfig
{
    /** @var FormFactory */
    private $formFactory;

    /**
     * ConfigForm constructor.
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Build the configuration form
     *
     * @return FormInterface
     */
    public function buildForm(): FormInterface
    {
        $options = [
            'csrf_protection' => false, // because it's rendered in an iframe
        ];

        $builder = $this->formFactory->createBuilder(
            FormType::class,
            $this->getDefaultValues(),
            $options
        );

        $builder
            ->add('release__enabled', CheckboxType::class, ['required' => false, 'label' => 'Enable feature'])
            ->add('submit', SubmitType::class);

        return $builder->getForm();
    }

    /**
     * Get the default values from the config file
     *
     * @return array
     */
    public function getDefaultValues(): array
    {
        $currentConfig = new ToolboxConfig();
        $formatter = new ArrayFormatter();
        return $formatter->toFlatArray($currentConfig->toArray());
    }
}
