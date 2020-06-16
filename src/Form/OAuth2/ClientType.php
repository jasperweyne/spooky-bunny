<?php

namespace App\Form\OAuth2;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Event\PostSetDataEvent;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Trikoder\Bundle\OAuth2Bundle\Model\Client;
use Trikoder\Bundle\OAuth2Bundle\Model\Grant;
use Trikoder\Bundle\OAuth2Bundle\Model\RedirectUri;
use Trikoder\Bundle\OAuth2Bundle\Model\Scope;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active', CheckboxType::class, [
                'label' => 'Actief',
                'required' => false,
            ])
            ->add('plainTextPkce', CheckboxType::class, [
                'label' => 'Ongehashte PKCE toegestaan',
                'mapped' => false,
                'required' => false,
            ])
            ->add('redirects', TextareaType::class, [
                'label' => 'Redirect Uris',
                'mapped' => false,
                'help' => '1 uri per regel',
            ])
            ->add('grants', ChoiceType::class, [
                'choices' => [
                    'authorization_code' => 'authorization_code',
                    'client_credentials' => 'client_credentials',
                    'implicit' => 'implicit',
                    'password' => 'password',
                    'refresh_token' => 'refresh_token',
                ],
                'expanded' => true,
                'multiple' => true,
                'mapped' => false,
            ])
            ->add('scopes', ChoiceType::class, [
                'choices' => [
                    'openid' => 'openid',
                    'admin' => 'admin',
                    'profile' => 'profile',
                    'email' => 'email',
                ],
                'expanded' => true,
                'multiple' => true,
                'mapped' => false,
            ])
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                function(PostSetDataEvent $event) {
                    $entity = $event->getData();
                    $builder = $event->getForm();

                    $builder->get('plainTextPkce')->setData($entity->isPlainTextPkceAllowed());
                    $builder->get('redirects')->setData(implode('\n', array_map(function ($x) {
                        return strval($x);
                    }, $entity->getRedirectUris())));
                    $builder->get('grants')->setData($entity->getGrants());
                    $builder->get('scopes')->setData($entity->getScopes());
                }
            )
            ->addEventListener(
                FormEvents::POST_SUBMIT,
                function(PostSubmitEvent $event) {
                    $entity = $event->getForm()->getData();
                    $form = $event->getForm();

                    $entity->setAllowPlainTextPkce($form->get('plainTextPkce')->getData());

                    $entity->setRedirectUris(...array_map(function ($s) {
                        return new RedirectUri($s);
                    }, explode('\n', $form->get('redirects')->getData())));

                    $entity->setGrants(...array_map(function ($s) {
                        return new Grant($s);
                    }, $form->get('grants')->getData()));

                    $entity->setScopes(...array_map(function ($s) {
                        return new Scope($s);
                    }, $form->get('scopes')->getData()));
                }
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
