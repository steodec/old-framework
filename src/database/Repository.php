<?php

namespace Humbrain\Framework\database;

use Framework\Database\PaginatedQuery;
use Framework\Database\Query;
use Humbrain\Framework\Exceptions\NoRecordException;
use Pagerfanta\Pagerfanta;
use PDO;

class Repository
{
    protected string $table_name;

    protected string|null $entity;
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return Query
     */
    public function makeQuery(): Query
    {
        return (new Query($this->pdo))
            ->from($this->table, $this->table[0])
            ->into($this->entity);
    }

    /**
     * @return string
     */
    protected function paginationQuery(): string
    {
        return "SELECT * FROM {$this->table_name}";
    }

    /**
     * @return Entity[]
     */
    public function findAll(): array
    {
        $query = $this->pdo->query("SELECT * FROM {$this->table_name}");
        if ($this->entity === null) :
            return $query->fetchAll();
        endif;
        $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        return $query->fetchAll();
    }

    /**
     * @param string $key
     * @param string $value
     * @return Entity
     * @throws NoRecordException
     */
    public function findBy(string $key, string $value): Entity
    {
        $record = $this->fetchOrFail("SELECT * FROM {$this->table_name} WHERE $key = ?", [$value]);
        if ($record === false) :
            throw new NoRecordException($this->table_name, $key, $value);
        endif;
        return $record;
    }

    /**
     * @throws NoRecordException
     */
    protected function fetchOrFail(string $query, array $params = [])
    {
        $query = $this->pdo->prepare($query);
        $query->execute($params);
        if ($this->entity !== null) :
            $query->setFetchMode(PDO::FETCH_CLASS, $this->entity);
        endif;
        $result = $query->fetch();
        if ($result === false) {
            throw new NoRecordException("Aucun résultat n'a été trouvé");
        }
        return $result;
    }

    /**
     * @param string $key
     * @param string $value
     * @return array
     */
    public function findList(string $key, string $value): array
    {
        $query = $this->pdo->query("SELECT * FROM {$this->table_name}");
        $result = $query->fetchAll();
        $return = [];
        foreach ($result as $item) {
            $return[$item->$key] = $item->$value;
        }
        return $return;
    }

    /**
     * @param int $id
     * @return Entity
     * @throws NoRecordException
     */
    public function find(int $id): Entity
    {
        return $this->fetchOrFail("SELECT * FROM {$this->table_name} WHERE id = ?", [$id]);
    }

    /**
     * @param int $id
     * @param array $fields
     * @return bool
     */
    public function update(int $id, array $fields): bool
    {
        $sqlFields = $this->buildFieldQuery($fields);
        $smtp = $this->pdo->prepare("UPDATE {$this->table_name} SET $sqlFields WHERE id = :id");
        $smtp->bindParam(':id', $id, PDO::PARAM_INT);
        foreach ($fields as $k => $v) :
            $smtp->bindValue(":$k", $v);
        endforeach;
        return $smtp->execute();
    }

    /**
     * @param array $fields
     * @return string
     */
    private function buildFieldQuery(array $fields): string
    {
        return join(', ', array_map(fn($k) => "$k = :$k", array_keys($fields)));
    }

    public function create(object|array $params): bool
    {
        $sqlFields = $this->buildFieldQuery($params);
        $smtp = $this->pdo->prepare("INSERT INTO {$this->table_name} SET $sqlFields");
        return $smtp->execute($params);
    }

    public function delete(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table_name} WHERE id = ?");
        return $query->execute([$id]);
    }

    /**
     * @return string|null
     */
    public function getTableName(): ?string
    {
        return $this->table_name;
    }

    /**
     * @return string|null
     */
    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function exists(mixed $value, ?string $fields = null, ?int $exclude = null): bool
    {
        $value = [$value];
        if (is_null($fields)) :
            $query = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE id = ?");
        else :
            if (is_null($exclude)) :
                $query = $this->pdo->prepare("SELECT * FROM {$this->table_name} WHERE $fields = ?");
            else :
                $query = $this->pdo->prepare(
                    "SELECT * FROM {$this->table_name} WHERE $fields = ? AND id != ?"
                );
                $value[] = $exclude;
            endif;
        endif;
        $query->execute($value);
        return $query->fetch() !== false;
    }
}
