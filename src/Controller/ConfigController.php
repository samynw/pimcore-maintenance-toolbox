<?php

namespace MaintenanceToolboxBundle\Controller;

use MaintenanceToolboxBundle\Service\FormBuilder\EditConfig;
use MaintenanceToolboxBundle\Tool\ArrayFormatter;
use MaintenanceToolboxBundle\Config\ToolboxConfig;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AdminController
{
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var EditConfig */
    private $formService;

    /**
     * ConfigController constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param EditConfig $formService
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EditConfig $formService
    )
    {
        $this->formFactory = $formFactory;
        $this->formService = $formService;
    }

    /**
     * Show the form to edit the configuration
     * and update the config file upon saving
     *
     * @param Request $request
     * @return Response
     * @Route("/admin/maintenance-toolbox/config", name="maintenancetoolbox_config")
     */
    public function configAction(Request $request): Response
    {
        $config = new ToolboxConfig();

        $form = $this->formFactory->create(
            $this->formService->getFormClassName(),
            $this->formService->getDefaultValues(),
            $this->formService->getDefaultOptions()
        )->add('submit', SubmitType::class, ['label' => 'Save config']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $formatter = new ArrayFormatter();
            $config->save($formatter->toNestedArray($data));
        }

        return $this->render(
            '@MaintenanceToolbox/config/config.html.twig',
            [
                'config' => $config,
                'form' => $form->createView(),
            ]
        );
    }
}
