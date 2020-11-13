<?php

namespace MaintenanceToolboxBundle\Controller;

use MaintenanceToolboxBundle\Form\EditConfig;
use MaintenanceToolboxBundle\Tool\ArrayFormatter;
use MaintenanceToolboxBundle\Config\ToolboxConfig;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfigController extends AdminController
{
    /** @var EditConfig */
    private $editForm;

    /**
     * ConfigController constructor.
     *
     * @param EditConfig $formService
     */
    public function __construct(EditConfig $formService)
    {
        $this->editForm = $formService;
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

        $form = $this->editForm->buildForm();
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
