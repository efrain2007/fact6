<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\JsonResource;

class ConfigurationEcommerceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'information_contact_name' => $this->information_contact_name,
            'information_contact_email' =>  $this->information_contact_email,
            'information_contact_phone' =>  $this->information_contact_phone,
            'script_paypal' => $this->script_paypal,
            'token_private_culqui' => $this->token_private_culqui,
            'token_public_culqui' => $this->token_public_culqui,

        ];
    }
}
