<?php declare(strict_types=1);

namespace App\Domains\Vehicle\ControllerApi;

use Illuminate\Http\JsonResponse;
use App\Domains\Vehicle\Model\Vehicle as Model;
use App\Domains\Vehicle\Service\ControllerApi\Update as ControllerService;

class Update extends ControllerApiAbstract
{
    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(int $id): JsonResponse
    {
        $this->row($id);

        return $this->json($this->factory()->fractal('json', $this->execute()));
    }

    /**
     * @return \App\Domains\Vehicle\Model\Vehicle
     */
    protected function execute(): Model
    {
        return $this->action(data: $this->data())->update();
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }
}
