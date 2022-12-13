<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 *
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function save(Contact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * @return Contact[] Returns an array of Contact objects
    */
    public function getExistingContacts(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id, c.idContact, c.civ, c.prenom, c.nom, c.tel, c.modele, c.email, c.cp, c.statut')
            ->orderBy('c.idContact', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
    * @return Contact[] Returns an array of Contact objects
    */
    public function getCampainContacts(): array
    {
        return $this->createQueryBuilder('c')
                ->andWhere('c.statut = ?1')
                ->orWhere('c.statut = ?2')
                ->orWhere('c.statut = ?3')
                ->orWhere('c.statut is NULL')
                ->setParameters([
                    1 => 'NRP/OQP',
                    3 => 'BARRAGE SECRETAIRE',
                    2 => 'RAPPEL'])
                ->getQuery()
                ->getResult();
    }

    /**
    * @return Contact[] Returns an array of Contact objects
    */
    public function getLeads(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.statut = ?1')
            ->andWhere('c.echeanceProjet = ?2')
            ->andWhere('c.transmitted IS NULL')
            ->setParameters([
                1 => 'PROJET AVEC RAPPEL COMMERCIAL',
                2 => 'Moins de 3 mois'])
            ->select('c.civ, c.prenom, c.nom, c.tel as telephone, c.modele, c.email, c.cp, c.campagne, c.idContact as idLead, c.codeConcession, c.statut, c.echeanceProjet')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Contact[] Returns an array of Contact objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Contact
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
