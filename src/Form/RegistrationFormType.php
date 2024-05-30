<?php

namespace App\Form;

use App\Entity\Usuario;
use App\Utils\Utils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'required' => false,
                'trim' => true,
                'constraints' => [
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'No puedes superar los 50 caracteres.',
                    ]),
                    new Callback([
                        $this, 'validar_nombre_apellidos_registro',
                    ]),
                ]
            ])
            ->add('apellidos', TextType::class, [
                'required' => false,
                'trim' => true,
                'constraints' => [
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'No puedes superar los 100 caracteres.',
                    ]),
                    new Callback([
                        $this, 'validar_nombre_apellidos_registro',
                    ]),
                ]
            ])
            ->add('dni', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, introduce tu Dni.',
                    ]),
                    new Length([
                        'min' => 9,
                        'max' => 9,
                        'exactMessage' => 'El dni debe ser de 9 caracteres.',
                    ]),
                    new Callback([
                        $this, 'validar_dni_registro',
                    ]),
                ],
            ])
            ->add('pais', ChoiceType::Class, [
                'choices' => $options['paises'],
            ])
            ->add('provincia', ChoiceType::Class, [
                'choices' => $options['es'],
            ])
            ->add('cp', NumberType::class, [
                'required' => false,
                'trim' => true,
            ])
            ->add('direccion', TextType::class, [
                'required' => false,
                'trim' => true,
                'constraints' => [
                    new Length([
                        'max' => 100,
                        'maxMessage' => 'No puedes superar los 100 caracteres.',
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'trim' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, introduce tu email.',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'required' => true,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, introduce una contraseña.',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Tu contraseña debe de tener al menos {{ limit }} caracteres.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('foto', FileType::class, [
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '3072k',
                        'maxSizeMessage' => 'La foto introducida supera el limite de tamaño (3mb).',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Por favor, sube un fichero válido (JPG, JPEG o PNG).',
                    ]),
                ],
            ])
            ->add('aceptarTerminosCondiciones', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Debes de aceptar nuestros términos y condiciones.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'paises' => [],
            'es' => [],
        ]);
    }

    public function validar_dni_registro($dni, ExecutionContextInterface $context)
    {
        if (!Utils::validar_dni($dni)) {
            $context->buildViolation('El Dni introducido es inválido.')
                ->atPath('dni')
                ->addViolation();
        }
    }

    public function validar_nombre_apellidos_registro($texto, ExecutionContextInterface $context)
    {
        if (!Utils::validar_nombre_apellidos($texto)) {
            $context->buildViolation('El texto introducido en el campo es inválido.')
                ->atPath('nombre')
                ->atPath('apellidos')
                ->addViolation();
        }
    }
}
