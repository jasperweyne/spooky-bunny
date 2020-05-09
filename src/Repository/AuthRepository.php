<?php

namespace App\Repository;

use App\Entity\Security\Auth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use OpenIDConnectServer\Repositories\IdentityProviderInterface;

/**
 * @method Auth|null find($id, $lockMode = null, $lockVersion = null)
 * @method Auth|null findOneBy(array $criteria, array $orderBy = null)
 * @method Auth[]    findAll()
 * @method Auth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthRepository extends ServiceEntityRepository implements IdentityProviderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Auth::class);
    }

    public function getUserEntityByIdentifier($identifier): Auth
    {
        $userEntity = $this->createQueryBuilder('p')
            ->andWhere('p.id = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getResult()
        ;

        return cast(Auth::class, (object) $userEntity);
    }

    // /**
    //  * @return Auth[] Returns an array of Auth objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Auth
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

//Sourced from
//https://stackoverflow.com/questions/3243900/convert-cast-an-stdclass-object-to-another-class
/**
 * Class casting.
 *
 * @param string|object $destination
 * @param object        $sourceObject
 *
 * @return object
 */
function cast($destination, $sourceObject)
{
    if (is_string($destination)) {
        $destination = new $destination();
    }
    $sourceReflection = new \ReflectionObject($sourceObject);
    $destinationReflection = new \ReflectionObject($destination);
    $sourceProperties = $sourceReflection->getProperties();
    foreach ($sourceProperties as $sourceProperty) {
        $sourceProperty->setAccessible(true);
        $name = $sourceProperty->getName();
        $value = $sourceProperty->getValue($sourceObject);
        if ($destinationReflection->hasProperty($name)) {
            $propDest = $destinationReflection->getProperty($name);
            $propDest->setAccessible(true);
            $propDest->setValue($destination, $value);
        } else {
            $destination->$name = $value;
        }
    }

    return $destination;
}
