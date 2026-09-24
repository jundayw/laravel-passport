<?php

namespace Jundayw\Passport\Model;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Jundayw\Passport\Contracts\Model\Passport as PassportModel;
use Jundayw\Passport\Exceptions\PassportDisabledException;
use Jundayw\Passport\Exceptions\PassportNotFoundException;

class Passport extends Model implements PassportModel
{
    use SoftDeletes;

    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = 'updated_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const DELETED_AT = 'deleted_at';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = [];

    /**
     * Get the current connection name for the model.
     *
     * @return string|null
     */
    public function getConnectionName(): ?string
    {
        return $this->connection ?? config('passport.database.connection');
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table ?? config('passport.database.table', parent::getTable());
    }

    protected function state(): Attribute
    {
        return new Attribute(
            get: fn($value, $attributes) => strtolower($value),
            set: fn($value, $attributes) => strtoupper($value),
        );
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param DateTimeInterface $date
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat);
    }

    /**
     * Retrieve the secret associated with the given key.
     *
     * @param string $key The identifier of the passport entry
     *
     * @return string The secret value
     *
     * @throws PassportNotFoundException If no model is found for the given key
     * @throws PassportDisabledException If the found model has a 'disable' state
     */
    public function getSecret(string $key): string
    {
        $passport = $this->resolvePayload($key);

        if (is_null($passport)) {
            throw new PassportNotFoundException(
                sprintf('Model not found for key: %s', $key)
            );
        }

        if ($passport->getAttribute('state') === 'disable') {
            throw new PassportDisabledException(
                sprintf('Model is disabled for key: %s', $key)
            );
        }

        return $passport->getAttribute('secret');
    }

    /**
     * Fetch the passport model from cache, or from the database and store it in the cache.
     *
     * @param string $key The identifier to look up
     *
     * @return Model|null The model instance if found, otherwise null
     */
    protected function resolvePayload(string $key): ?Model
    {
        $sentinel = static::class;
        $cached   = Cache::remember(
            "passport:{$key}",
            now()->addSeconds(config('passport.ttl.resolved')),
            function () use ($key, $sentinel) {
                $passport = $this->where(['key' => $key])->first();
                if (is_null($passport)) {
                    Cache::put(
                        "passport:{$key}",
                        $sentinel,
                        config('passport.ttl.fallback')
                    );
                    return $sentinel;
                }
                return $passport;
            }
        );

        return $cached === $sentinel ? null : $cached;
    }
}
