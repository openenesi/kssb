<?php

namespace App\Form;

use App\Entity\{
    Bank,
    CandidateBank
};
use Symfony\Component\Form\{
    AbstractType,
    FormBuilderInterface
};
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    SubmitType,
    TextType,
    ChoiceType
};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CandidateBankType extends AbstractType {

    use \App\Utility\Utils;

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('bvn', TextType::class, array("label" => "Unique BVN: ",
                    "attr" => array("placeholder" => "eg. 12345678901"), "data"=>$options['candidate']->getBvn()))
                //->add('bvn', HiddenType::class ,array("data"=>$options['candidate']->getBvn()))
                ->add('bank', EntityType::class, array("label" => "Bank: ",
                    "attr" => array("class" => "custom-select"),
                    'class' => Bank::class,
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('t')
                                ->orderBy('t.bankName', 'ASC');
                    },
                    'choice_label' => function ($bank) {
                        return $bank->getBankName();
                    },
                    /* 'choice_attr' => function ($bank) use ($options) {
                      return ($bank->getId()==$options['bank']->getId())?["selected"=>"selected"]:"";
                      }, */
                    "placeholder" => "Select Bank"))
                ->add('accountName', TextType::class, array("label" => "Account Name: ", "data"=>$options['candidate']->getCandidatePersonal()->getFullName(),  "attr" => array("placeholder" => "Eg." .($options['candidate']->getCandidatePersonal()->getFullName()))))
                ->add('accountNo', TextType::class, array("label" => "NUBAN Account No.: ",
                    "attr" => array("placeholder" => "eg. 1234567890")))
                ->add('accountNo2', TextType::class, array("label" => "Re-Type Account No.: ", "mapped"=>false,
                    "data"=>(($options['candidate']->getCandidateBank())?($options['candidate']->getCandidateBank()->getAccountNo()):("")),
                    "attr" => array("placeholder" => "eg. 1234567890")))
                ->add('accountType', ChoiceType::class, array("label" => "Account Type (Optional): ",
                    "attr" => array("class" => "custom-control, custom-radio inline-check"),
                    "expanded"=>true,
                    "multiple"=>false,
                    "required"=>false,
                    "choices" => CandidateBankType::getUtilBankAccTypes(),
                    'choice_label' => function ($value, $key, $index) {
                        return $key;
                    },
                    'choice_value' => function ($value) {
                        return $value;
                    },
                    "placeholder" => false))
                ->add('sortCode', TextType::class, array("label" => "Sort Code (Optional): ", "required"=>false, "attr" => array("placeholder" => "Eg. 456321")))
                ->add('save', SubmitType::class, array("label" => "Save & Proceed", "attr"=>array("class"=>"btn-primary")));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => CandidateBank::class,
            'csrf_protection' => false,
            'candidate' => null,
        ));
    }

}
