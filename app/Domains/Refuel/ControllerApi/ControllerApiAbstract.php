<?php declare(strict_types=1);

namespace App\Domains\Refuel\ControllerApi;

use App\Domains\CoreApp\Controller\ControllerApiAbstract as CoreControllerApiAbstract;
use App\Domains\Refuel\Model\Refuel as Model;

abstract class ControllerApiAbstract extends CoreControllerApiAbstract
{
    /**
     * @var ?\App\Domains\Refuel\Model\Refuel
     */
    protected ?Model $row;

    /**
     * @param int $id
     *
     * @return \App\Domains\Refuel\Model\Refuel
     */
    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->byUserOrManager($this->auth)
            ->firstOr(fn () => $this->exceptionNotFound(__('refuel.error.not-found')));
    }
}
