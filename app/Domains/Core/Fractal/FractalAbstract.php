<?php declare(strict_types=1);

namespace App\Domains\Core\Fractal;

use Illuminate\Support\Collection;
use App\Domains\Core\Model\ModelAbstract;
use App\Domains\Core\Traits\Factory;

abstract class FractalAbstract
{
    use Factory;

    /**
     * @param string $function
     * @param mixed $value
     * @param mixed ...$args
     *
     * @return ?array
     */
    final public function transform(string $function, $value, ...$args): ?array
    {
        if ($value === null) {
            return null;
        }

        if (empty($value)) {
            return [];
        }

        if ($value instanceof Collection) {
            return $this->collection($function, $value, $args);
        }

        if ($this->isArraySequential($value)) {
            return $this->sequential($function, $value, $args);
        }

        return $this->call($function, $value, $args);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    final protected function isArraySequential($value): bool
    {
        return is_array($value)
            && ($keys = array_keys($value))
            && ($keys === array_keys($value));
    }

    /**
     * @param string $function
     * @param \Illuminate\Support\Collection $value
     * @param array $args
     *
     * @return array
     */
    final protected function collection(string $function, Collection $value, array $args): array
    {
        return $value
            ->toBase()
            ->map(fn ($each) => $this->call($function, $each, $args))
            ->values()
            ->toArray();
    }

    /**
     * @param string $function
     * @param array $value
     * @param array $args
     *
     * @return array
     */
    final protected function sequential(string $function, array $value, array $args): array
    {
        return array_map(fn ($each) => $this->call($function, $each, $args), array_values($value));
    }

    /**
     * @param string $function
     * @param mixed $value
     * @param array $args
     *
     * @return ?array
     */
    final protected function call(string $function, $value, array $args): ?array
    {
        return $this->$function($value, ...$args);
    }

    /**
     * @param string $domain
     * @param string $view
     * @param mixed $data
     * @param mixed ...$args
     *
     * @return ?array
     */
    final protected function from(string $domain, string $view, $data, ...$args): ?array
    {
        return $this->factory($domain)->fractal($view, $data, ...$args);
    }

    /**
     * @param string $domain
     * @param string $view = 'simple'
     * @param \App\Domains\Core\Model\ModelAbstract $row
     * @param string $relation
     * @param mixed ...$args
     *
     * @return ?array
     */
    final protected function fromIfLoaded(string $domain, string $view, ModelAbstract $row, string $relation, ...$args): ?array
    {
        if ($row->relationLoaded($relation) === false) {
            return null;
        }

        return $this->from($domain, $view, $row->$relation, ...$args);
    }

    /**
     * @param string $domain
     * @param string $view = 'simple'
     * @param \App\Domains\Core\Model\ModelAbstract $row
     * @param string $relation
     * @param mixed ...$args
     *
     * @return ?array
     */
    final protected function fromIfLoadedOrId(string $domain, string $view, ModelAbstract $row, string $relation, ...$args): ?array
    {
        $return = $this->fromIfLoaded($domain, $view, $row, $relation, ...$args);

        if ($return) {
            return $return;
        }

        $id = $row->{$relation.'_id'};

        if ($id) {
            return ['id' => $id];
        }

        return null;
    }
}
