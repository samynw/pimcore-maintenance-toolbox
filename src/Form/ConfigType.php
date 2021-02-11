<?php

namespace Samynw\MaintenanceToolboxBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfigType extends AbstractType
{
    /**
     * Build the edit configuration form
     * Currently only the checkbox for enabling the release command
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return \Symfony\Component\Form\FormInterface|void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'release__enabled',
            CheckboxType::class,
            ['required' => false, 'label' => 'Enable feature']
        );
    }
}
