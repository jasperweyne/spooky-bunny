<?php

namespace App\Form\Person;

use App\Entity\Person\PersonField;
use App\Form\Person\Dynamic\DynamicTypeRegistry;
use App\Security\ClaimExtractor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonFieldType extends AbstractType
{
    private $typeRegistry;
    private $claims;

    public function __construct(DynamicTypeRegistry $typeRegistry, ClaimExtractor $claims)
    {
        $this->typeRegistry = $typeRegistry;
        $this->claims = $claims;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('slug', ChoiceType::class) // see event listeners
            ->add('userEditOnly', CheckboxType::class, [
                'label' => 'Alleen aanpassen door gebruiker?',
                'required' => false,
            ])
            ->add('valueType', ChoiceType::class, [
                'choice_loader' => new CallbackChoiceLoader(function () {
                    $vals = array_keys($this->typeRegistry->getTypes());

                    return array_combine($vals, $vals);
                }),
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $builder = $event->getForm();
            $field = $event->getData();

            $choices = [];

            if ($field) {
                $choices[$field->getSlug()] = $field->getSlug();
            }

            foreach ($this->claims->getClaimSets() as $scope => $claimSet) {
                if ($scope == "email") continue;
                
                $append = array_combine($claimSet->getClaims(), $claimSet->getClaims());
                $choices[$scope] = $append;
            }

            $builder->add('slug', ChoiceType::class, [
                'attr' => ['tags' => 'true'],
                'label' => 'Expressie naam',
                'required' => false,
                'choices' => $choices,
            ]);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            // Get the parent form
            $form = $event->getForm();
            
            // Get the data for the choice field
            $data = $event->getData()['slug'];
            
            // Collect the new choices
            $choices = array();
            
            if (is_array($data)) {
                foreach($data as $choice) {
                    $choices[$choice] = $choice;
                }
            } else {
                $choices[$data] = $data;
            }
            
            // Add the field again, with the new choices :
            $form->add('slug', ChoiceType::class, ['choices' => $choices]);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PersonField::class,
        ]);
    }
}
