<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieFormType extends AbstractType
{
    public function __construct(private CategorieRepository $categorieRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $idCat =  0; $nb = 0;
        if (isset($options["data"])){
            $idCat = $options["data"]->getId() ;
            $nb = $this->categorieRepository->countChildCategories($idCat);

        }

        if ($nb == 0){
            $builder
                ->add('libelle')
                ->add('CategorieParente', EntityType::class, [
                    'class' => Categorie::class,
                    'query_builder' => function (CategorieRepository $categorieRepository) use ($idCat)  {
                        return $categorieRepository->createQueryBuilder('c')
                            ->orderBy('c.libelle', 'ASC')
                            ->andWhere('c.id != :id')
                            ->andWhere('c.CategorieParente is null')
                            ->andWhere('c.deleted = false')
                            ->setParameter("id", $idCat);
                    },
                    'required' => false,
                ])
            ;
        }else {
            $builder
                ->add('libelle')
            ;
        }


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
    // TODO: Find a way to remove the edited cat. from the select
}
