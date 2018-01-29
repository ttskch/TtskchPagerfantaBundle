<?php

namespace Ttskch\PagerfantaBundle;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Ttskch\PagerfantaBundle\Entity\Criteria;
use Ttskch\PagerfantaBundle\Form\CriteriaType;

class Context
{
    /**
     * @var Criteria
     */
    public $criteria;

    /**
     * @var Form
     */
    public $form;

    /**
     * @var Config
     */
    public $config;

    /**
     * @var null|Request
     */
    public $request;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(Config $config, RequestStack $requestStack, FormFactoryInterface $formFactory)
    {
        $this->config = $config;
        $this->request = $requestStack->getCurrentRequest();
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $defaultSortKey
     * @param string $criteriaClass
     * @param string $formTypeClass
     * @param Criteria|null $criteria
     * @return $this
     */
    public function initialize($defaultSortKey, $criteriaClass = Criteria::class, $formTypeClass = CriteriaType::class, Criteria $criteria = null)
    {
        $this->criteria = $criteria ?: new $criteriaClass($this->config->limitDefault, $defaultSortKey, $this->config->sortDirectionDefault);

        $this->form = $this->formFactory->createNamed('', $formTypeClass, $this->criteria, [
            'method' => 'GET',
        ]);

        $this->handleRequest();

        return $this;
    }

    /**
     * @return $this
     */
    public function handleRequest()
    {
        // Don't use Form::handleRequest() because it will clear properties corresponding empty queries.
        $this->form->submit($this->request->query->all(), false);

        return $this;
    }
}
