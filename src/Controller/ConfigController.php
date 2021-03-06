<?php

namespace Samynw\MaintenanceToolboxBundle\Controller;

use Samynw\MaintenanceToolboxBundle\Service\FormBuilder\EditConfig;
use Samynw\MaintenanceToolboxBundle\Tool\ArrayFormatter;
use Samynw\MaintenanceToolboxBundle\Config\ToolboxConfig;
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
    /** @var ToolboxConfig */
    private $config;

    /**
     * ConfigController constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param EditConfig $formService
     * @param ToolboxConfig $config
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EditConfig $formService,
        ToolboxConfig $config
    )
    {
        $this->formFactory = $formFactory;
        $this->formService = $formService;
        $this->config = $config;
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
        $this->config = new ToolboxConfig();

        $form = $this->formFactory->create(
            $this->formService->getFormClassName(),
            $this->formService->getDefaultValues(),
            $this->formService->getDefaultOptions()
        )->add('submit', SubmitType::class, ['label' => 'Save config']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $formatter = new ArrayFormatter();
            $this->config->save($formatter->toNestedArray($data));
        }

        return $this->render(
            '@MaintenanceToolbox/config/config.html.twig',
            [
                'config' => $this->config,
                'form' => $form->createView(),
            ]
        );
    }
}
