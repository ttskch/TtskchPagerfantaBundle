<?php

namespace Ttskch\PagerfantaBundle;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Ttskch\PagerfantaBundle\Entity\Criteria;
use Ttskch\PagerfantaBundle\Form\CriteriaType;

class Paginator
{
    /**
     * @var Config
     */
    public $config;

    /**
     * @var Criteria
     */
    public $criteria;

    /**
     * @var Form
     */
    public $form;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(Config $config, FormFactoryInterface $formFactory)
    {
        $this->config = $config;
        $this->formFactory = $formFactory;
    }

    /**
     * @param $defaultSortKey
     * @return $this
     */
    public function initialize($defaultSortKey, $criteriaClass = Criteria::class, $formTypeClass = CriteriaType::class)
    {
        $this->criteria = new $criteriaClass($this->config->limitDefault, $defaultSortKey, $this->config->sortDirectionDefault);

        $this->form = $this->formFactory->createNamed('', $formTypeClass, $this->criteria, [
            'method' => 'GET',
        ]);

        return $this;
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function handleRequest(Request $request)
    {
        // Don't use Form::handleRequest() because it will clear properties corresponding empty queries.
        $this->form->submit($request->query->all(), false);

        return $this;
    }
}
