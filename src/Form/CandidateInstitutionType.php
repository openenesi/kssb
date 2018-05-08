<?php

namespace App\Form;

use App\Entity\{
    CandidateInstitution,
    Institution
};
use Symfony\Component\Form\{
    AbstractType,
    FormBuilderInterface
};
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{
    DateType,
    TextType,
    ChoiceType,
    TextareaType,
    SubmitType
};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CandidateInstitutionType extends AbstractType {

    use \App\Utility\Utils;

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $dt = new \DateTime();
        $year = $dt->format("Y");
        $builder
                ->add('matricNo', TextType::class, array("label" => "Matriculation/Registration No.: ",
                    "attr" => array("placeholder" => "eg. U06CS1003"), "data" => $options['candidate']->getMatricNo()))
                //->add('matricNo', HiddenType::class, array("data" => $options['candidate']->getMatricNo()))
                ->add('institutionCategory', ChoiceType::class, array("label" => "Institution Category: ",
                    "attr" => array("class" => "custom-select"),
                    "mapped" => false,
                    "choices" => CandidateInstitutionType::getUtilInstitutionCategories(),
                    'choice_label' => function ($value, $key, $index) {
                        return $key;
                    },
                    'choice_value' => function ($value) {
                        return $value;
                    },
                    'choice_attr' => function ($category) use ($options) {
                        return (isset($options['category']) && $category == $options['category']) ? ["selected" => "selected"] : [];
                    },
                    "placeholder" => "Select Category"))
                ->add('institution', EntityType::class, array("label" => "Institution: ",
                    "attr" => array("class" => "custom-select"),
                    'class' => Institution::class,
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('t')
                                ->where('t.institutionCategory = :cat')
                                ->orderBy('t.institutionName', 'ASC')
                                ->setParameter("cat", $options['category']);
                    },
                    'choice_label' => function ($institution) {
                        return $institution->getInstitutionName();
                    },
                    "placeholder" => "Select Institution"))
                ->add('institutionAddr', TextareaType::class, array("label" => "Institution Address: ", "attr" => array("placeholder" => "Address of institution")))
                ->add('faculty', TextType::class, array("label" => "Faculty/School: ", "attr" => array("placeholder" => "Eg. Faculty of Physical Sciences")))
                ->add('department', TextType::class, array("label" => "Department: ", "attr" => array("placeholder" => "Eg. Computer Science")))
                ->add('courseOfStudy', TextType::class, array("label" => "Course of Study: ", "attr" => array("placeholder" => "Eg. Software Engineering")))
                ->add('level', TextType::class, array("label" => "Level: ", "attr" => array("placeholder" => "Eg. 100 Level")))
                ->add('courseDuration', ChoiceType::class, array("label" => "Course Duration (Years): ",
                    "attr" => array("class" => "custom-select"),
                    "choices" => range(1, 7),
                    'choice_label' => function ($value, $key, $index) {
                        return $value . " Year" . (($value > 1) ? ("s") : (""));
                    },
                    "placeholder" => "Select Duration"))
                ->add('admissionDate', DateType::class, array("label" => "Date of Admission: ",
                    "format" => "yyyy-MM-dd",
                    "html5" => false,
                    "widget" => "single_text",
                    "attr" => array("data-date-max-view-mode" => "2", "data-date-format" => "yyyy-mm-dd", "data-date-autoclose" => "true", "data-date-end-date" => "0d", "data-provide" => "datepicker", "class" => "date", 'placeholder' => "YYYY-MM-DD")
                ))
                ->add('graduationYear', ChoiceType::class, array("label" => "Graduation Year: ",
                    "attr" => array("class" => "custom-select"),
                    "choices" => range($year, $year + 7),
                    'choice_label' => function ($value, $key, $index) {
                        return $value;
                    },
                    "placeholder" => "Select Year"))
                ->add('accommodationType', ChoiceType::class, array("label" => "Accommodation: ",
                    "attr" => array("class" => "custom-control custom-radio inline-check"),
                    "expanded" => true,
                    "multiple" => false,
                    "choices" => CandidateInstitutionType::getUtilAccommodationTypes(),
                    'choice_label' => function ($value, $key, $index) {
                        return $key;
                    },
                    'choice_value' => function ($value) {
                        return $value;
                    },
                    "placeholder" => false))
                //->add('save2', SubmitType::class, array("label" => "Save"))
                ->add('save', SubmitType::class, array("label" => "Save & Proceed", "attr"=>array("class"=>"btn-primary")));
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => CandidateInstitution::class,
            'csrf_protection' => false,
            'category' => null,
            'candidate' => null,
        ));
    }

}
