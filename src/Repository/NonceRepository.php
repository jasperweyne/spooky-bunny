<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Security\Nonce;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class NonceRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findByRequest(string $s): ?Nonce
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from(Nonce::class, 'p')
            ->andWhere('p.requestId = :val')
            ->setParameter('val', $s)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByAuthCode(string $code): ?Nonce
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('p')
            ->from(Nonce::class, 'p')
            ->andWhere('p.codeId = :val')
            ->setParameter('val', $code)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function save(Nonce $nonce): void
    {
        $this->entityManager->persist($nonce);
        $this->entityManager->flush();
    }

    public function clearExpired(): int
    {
        return $this->entityManager->createQueryBuilder()
            ->delete(Nonce::class, 'ac')
            ->where('ac.expiry < :expiry')
            ->setParameter('expiry', new DateTimeImmutable())
            ->getQuery()
            ->execute();
    }
}
