<?php

namespace App\Repositories;

use App\Models\User;

/**
 * @extends Repository<User>
 * @implements HasItems<Item>
 * @method static Builder query()
 * @property-read Settings $settings
 * @mixin Macros
 * @see Helper
 * @see https://example.com/docs
 * @see format()
 */
class UserRepository extends Repository
{
    /** @var array<string, Profile> */
    private array $profiles = [];

    /**
     * @param Criteria|null $criteria
     * @param list<Filter> $filters
     * @param array{sort: Sorting, page?: Page} $options
     * @return Collection<int, User>
     * @throws NotFound
     * @phpstan-return Paginator<User>
     */
    public function find($criteria, array $filters, array $options)
    {
    }

    /** @var int|string $id This mentions Prose but is not a type */
    private $id;
}
