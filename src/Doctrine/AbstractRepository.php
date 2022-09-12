<?php

namespace Beautystack\Database\Symfony\Doctrine;

use Beautystack\Database\Contracts\AbstractRepository as AbstractRepositoryBase;
use Beautystack\Database\Contracts\CacheHandlerInterface;
use Beautystack\Database\Contracts\Exception\DuplicateEntityException;
use Beautystack\Database\Contracts\Exception\EntityNotFoundException;
use Beautystack\Database\Contracts\ModelInterface;
use Beautystack\Database\Contracts\RepositoryInterface;
use Beautystack\Value\Contracts\Identity\Id;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractRepository extends AbstractRepositoryBase implements RepositoryInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        null|CacheHandlerInterface $cacheHandler = null
    ) {
        parent::__construct($cacheHandler);
    }

    /**
     * @return class-string
     */
    abstract protected function getModelClass() : string;

    /**
     * @throws EntityNotFoundException
     */
    protected function findOneByField(string $fieldName, string $fieldValue): ModelInterface
    {
        /** @var ModelInterface $model */
        $model = $this->entityManager
            ->getRepository($this->getModelClass())
            ->findOneBy([
                $fieldName => $fieldValue,
            ]);

        if (empty($model)) {
            throw new EntityNotFoundException(sprintf('%s with %s %s not found', $this->getModelClass(), $fieldName, $fieldValue));
        }

        return $model;
    }

    /**
     * @throws DuplicateEntityException
     */
    protected function store(ModelInterface $model): void
    {
        try {
            $this->entityManager->persist($model);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $e) {
            throw new DuplicateEntityException('That entity already exists', '', 0, $e);
        }
    }


    public function exists(Id $id): bool
    {
        return $this->entityManager
                ->createQueryBuilder()
                ->select('COUNT(f) as numItems')
                ->from($this->getModelClass(), 'f')
                ->where('f.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getArrayResult()[0]['numItems'] > 0;
    }

}