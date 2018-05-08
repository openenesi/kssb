<?php

namespace App\Form;

use App\Entity\{
    User
};
use Symfony\Component\Form\{
    AbstractType,
    FormBuilderInterface,
    FormEvent,
    FormEvents
};
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    DateTimeType,
    MoneyType,
    IntegerType,
    SubmitType,
    TextType,
    ChoiceType, 
    EmailType,
    TelType
};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserType extends AbstractType {

    use \App\Utility\Utils;

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('matricNo', TextType::class, array("label" => "Matriculation/Registration No.: ",
                    "attr"=> array("placeholder" => "eg. U06CS1003")))
                ->add('bvn', TextType::class, array("label" => "Unique BVN: ",
                    "attr"=> array("placeholder" => "eg. 12345678901")))
                ->add('email', EmailType::class, array("label" => "Email: ", "attr"=> array("placeholder"=>"eg. mymailid@gmail.com")))
                ->add('email2', EmailType::class, array("label" => "Re-Type Email: ", "mapped"=>false, "attr"=> array("placeholder"=>"re-type email")))
                ->add('mobileNo', TelType::class, array("label" => "Mobile No.: ", "attr"=>array("placeholder"=>"08012345678")))
                ->add('create', SubmitType::class, array("label" => "Create Account"));


    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'csrf_protection' => false,
        ));
    }

}
