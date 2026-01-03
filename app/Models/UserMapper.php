<?php

/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright (c) 2015 - 2026 Wolfgang Moritz
 * @license   https://github.com/PitonCMS/Piton/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Piton\Models;

use Piton\Models\Entities\PitonEntity;
use Piton\ORM\DataMapperAbstract;

/**
 * Piton User Mapper
 */
class UserMapper extends DataMapperAbstract
{
    protected string $table = 'user';
    protected array $modifiableColumns = ['first_name', 'last_name', 'email', 'role', 'active'];
    protected string $domainValueObjectClass = __NAMESPACE__ . '\Entities\User';

    /**
     * Find All Users
     *
     * Sorts by active
     * @param void
     * @return ?array
     */
    public function findUsers(): ?array
    {
        $this->makeSelect();
        $this->sql .= " order by `created_date`;";

        return $this->find();
    }

    /**
     * Find Active User by Email
     *
     * @param string $email
     * @return ?array
     */
    public function findActiveUserByEmail(string $email): ?PitonEntity
    {
        $this->makeSelect();
        $this->sql .= " and `active` = 'Y' and `email` = ?;";
        $this->bindValues[] = $email;

        return $this->findRow();
    }
}
