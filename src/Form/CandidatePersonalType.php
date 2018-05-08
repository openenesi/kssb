<?php

namespace App\Form;

use App\Entity\{
    Lga,
    CandidatePersonal,
    Ward
};
use Symfony\Component\Form\{
    AbstractType,
    FormBuilderInterface
};
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    DateType,
    SubmitType,
    TextType,
    ChoiceType,
    EmailType,
    TelType,
    HiddenType,
    TextareaType
};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CandidatePersonalType extends AbstractType {

    use \App\Utility\Utils;

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('title', ChoiceType::class, array("label" => "Title (optional): ",
                    "required" => false,
                    "attr" => array("class" => "custom-select"),
                    "choices" => CandidatePersonalType::getUtilTitles(),
                    'choice_label' => function ($value, $key, $index) {
                        return $value;
                    },
                    "placeholder" => "Select Title"))
                ->add('surname', TextType::class, array("label" => "Surname: ", "attr" => array("placeholder" => "Eg. Umar")))
                ->add('firstName', TextType::class, array("label" => "Firstname Name: ", "attr" => array("placeholder" => "Eg. Ibrahim")))
                ->add('otherNames', TextType::class, array("label" => "Other Names (if any): ", "required" => false, "attr" => array("placeholder" => "Eg. Enesi")))
                ->add('dob', DateType::class, array("label" => "Date of Birth: ",
                    "format" => "yyyy-MM-dd",
                    "html5" => false,
                    "widget" => "single_text",
                    "attr" => array("data-date-max-view-mode" => "2", "data-date-start-view" => "2", "data-date-format" => "yyyy-mm-dd", "data-date-autoclose" => "true", "data-date-end-date" => "-15y", "data-provide" => "datepicker", "class" => "date", 'placeholder' => "YYYY-MM-DD")
                ))
                ->add('gender', ChoiceType::class, array("label" => "Gender: ",
                    "attr" => array("class" => "custom-control custom-radio inline-check"),
                    "expanded" => true,
                    "multiple" => false,
                    "choices" => CandidatePersonalType::getUtilGender(),
                    'choice_label' => function ($value, $key, $index) {
                        return $key;
                    },
                    'choice_value' => function ($value) {
                        return $value;
                    },
                    /* 'choice_attr' => function($value, $key, $index){
                      return ['class'=>'custom-control-input'];
                      }, */
                    "placeholder" => false))
                ->add('maritalStatus', ChoiceType::class, array("label" => "Marital Status: ",
                    "attr" => array("class" => "custom-select"),
                    "choices" => CandidatePersonalType::getUtilMaritalStatus(),
                    'choice_label' => function ($value, $key, $index) {
                        return $key;
                    },
                    'choice_value' => function ($value) {
                        return $value;
                    },
                    "placeholder" => "Select an option"))
                ->add('lga', EntityType::class, array("label" => "LGA: ",
                    "attr" => array("class" => "custom-select"),
                    'class' => Lga::class,
                    "mapped" => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('t')
                                ->orderBy('t.lgaName', 'ASC');
                    },
                    'choice_label' => function ($lga) {
                        return $lga->getLgaName();
                    },
                    'choice_attr' => function ($lga) use ($options) {
                        return (isset($options['lga']) && $lga->getId() == $options['lga']->getId()) ? ["selected" => "selected"] : [];
                    },
                    "placeholder" => "Select LGA"))
                ->add('ward', EntityType::class, array("label" => "Ward: ",
                    "attr" => array("class" => "custom-select"),
                    'class' => Ward::class,
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('t')
                                ->where('t.lga = :lga')
                                ->orderBy('t.wardName', 'ASC')
                                ->setParameter('lga', $options['lga']);
                    },
                    'choice_label' => function ($ward) {
                        return $ward->getWardName();
                    },
                    "placeholder" => "Select Ward"))
                ->add('tempEmail', EmailType::class, array("label" => "Email: ", "mapped" => false, "disabled" => true, "data" => $options['candidate']->getEmail(), "attr" => array("placeholder" => "mymailid@gmail.com")))
                ->add('email', HiddenType::class, array("data" => $options['candidate']->getEmail()))
                ->add('mobileNo', TelType::class, array("data" => $options['candidate']->getMobileNo(), "label" => "Mobile No.: ", "attr" => array("placeholder" => "08012345678")))
                //->add('mobileNo', HiddenType::class, array("label" => "Mobile No.: ", "data"=>$options['candidate']->getMobileNo(), "attr" => array("placeholder" => "08012345678")))
                ->add('homeAddr', TextareaType::class, array("label" => "Home Address: ", "attr" => array("placeholder" => "Full address")))
                ->add('nokName', TextType::class, array("label" => "Next of Kin Name: ", "attr" => array("placeholder" => "Eg. Mikailu")))
                ->add('nokAddr', TextareaType::class, array("label" => "Next of Kin Address: ", "attr" => array("placeholder" => "Full address of next of kin")))
                ->add('nokNo', TelType::class, array("label" => "NoK Mobile No.: ", "required" => false, "attr" => array("placeholder" => "08012345678")))
                //->add('save2', SubmitType::class, array("label" => "Save"))
                ->add('passport', HiddenType::class)
                ->add('save', SubmitType::class, array("label" => "Save & Proceed", "attr" => array("class" => "btn-primary")));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => CandidatePersonal::class,
            'csrf_protection' => false,
            'lga' => null,
            'candidate' => null,
        ));
    }

}
