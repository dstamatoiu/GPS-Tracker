<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class UpdateDeviceMessageUpdate extends ControllerAbstract
{
    /**
     * @param int $id
     * @param int $device_message_id
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function __invoke(int $id, int $device_message_id): Response|RedirectResponse
    {
        $this->row($id);
        $this->message($device_message_id);

        $this->actionPost('updateDeviceMessageDelete');

        return redirect()->route('device.update.device-message', $this->row->id);
    }

    /**
     * @return void
     */
    protected function updateDeviceMessageDelete(): void
    {
        $this->factory('DeviceMessage', $this->message)->action()->delete();

        $this->sessionMessage('success', __('device-update-device-message-update.delete-success'));
    }
}