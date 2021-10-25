<?php

namespace Modules\ApiPeruDev\Data;

use App\Models\Tenant\ExchangeRate;
use GuzzleHttp\Client;
use App\Models\System\Configuration;

class ServiceData
{
    protected $client;
    protected $parameters;

    public function __construct()
    {
        $configuration = Configuration::query()->first();
        $url = $configuration->url_apiruc =! '' ? $configuration->url_apiruc : config('configuration.api_service_url');
        $token = $configuration->token_apiruc =! '' ? $configuration->token_apiruc : config('configuration.api_service_token');

        $this->client = new Client(['base_uri' => $url]);
        $this->parameters = [
            'http_errors' => false,
            'connect_timeout' => 10,
            'verify' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ];
    }

    public function service($type, $number)
    {
        $res = $this->client->request('GET', '/api/'.$type.'/'.$number, $this->parameters);
        $response = json_decode($res->getBody()->getContents(), true);

        $res_data = [];
        if($response['success']) {
            $data = $response['data'];
            if($type === 'dni') {
                if(key_exists('source', $response) && $response['source'] === 'apiperu.dev') {
                    $department_id = $data['domicilio_ubigeo'][0];
                    $province_id = $data['domicilio_ubigeo'][1];
                    $district_id = $data['domicilio_ubigeo'][2];
                    $domicilio_direccion = $data['domicilio_direccion'];
                } else {
                    $department_id = $data['ubigeo'][0];
                    $province_id = $data['ubigeo'][1];
                    $district_id = $data['ubigeo'][2];
                    $domicilio_direccion = $data['direccion'];
                }

                $res_data = [
                    'name' => $data['nombre_completo'],
                    'trade_name' => '',
                    'location_id' => $district_id,
                    'address' => $domicilio_direccion,
                    'department_id' => $department_id,
                    'province_id' => $province_id,
                    'district_id' => $district_id,
                    'condition' => '',
                    'state' => '',
                ];
            }

            if($type === 'ruc') {
                $address = '';
                $department_id = null;
                $province_id = null;
                $district_id = null;
                if(key_exists('source', $response) && $response['source'] === 'apiperu.dev') {
                    $trade_name = key_exists('nombre_comercial', $data)?$data['nombre_comercial']:'';
                    if($data['direccion'] !== '') {
                        $address = $data['direccion'];
                        $department_id = $data['ubigeo'][0];
                        $province_id = $data['ubigeo'][1];
                        $district_id = $data['ubigeo'][2];
                    } else {
                        if($data['domicilio_direccion'] !== '') {
                            $address = $data['domicilio_direccion'];
                            $department_id = $data['domicilio_ubigeo'][0];
                            $province_id = $data['domicilio_ubigeo'][1];
                            $district_id = $data['domicilio_ubigeo'][2];
                        }
                    }
                } else {
                    $trade_name = $data['nombre_o_razon_social'];
                    $address = $data['direccion'];
                    $department_id = $data['ubigeo'][0];
                    $province_id = $data['ubigeo'][1];
                    $district_id = $data['ubigeo'][2];
                }

                $res_data = [
                    'name' => $data['nombre_o_razon_social'],
                    'trade_name' => $trade_name,
                    'address' => $address,
                    'department_id' => $department_id,
                    'province_id' => $province_id,
                    'district_id' => $district_id,
                    'condition' => $data['condicion'],
                    'state' => $data['estado'],
                ];
            }
            $response['data'] = $res_data;
        }

        return $response;
    }

    public function massive_validate_cpe($data)
    {
        $this->parameters['form_params'] = $data;
        $res = $this->client->request('POST', '/api/validacion_multiple_cpe', $this->parameters);

        return json_decode($res->getBody()->getContents(), true);
    }
    public function cpe($company_number, $document_type_id, $series, $number, $date_of_issue, $total)
    {
        $form_params = [
            'ruc_emisor' => $company_number,
            'codigo_tipo_documento' => $document_type_id,
            'serie_documento' => $series,
            'numero_documento' => $number,
            'fecha_de_emision' => $date_of_issue,
            'total' => $total
        ];

        $this->parameters['form_params'] = $form_params;
        $res = $this->client->request('POST', '/api/cpe', $this->parameters);

        return json_decode($res->getBody()->getContents(), true);
    }

    public function exchange($date)
    {
        $exchange = ExchangeRate::query()->where('date', $date)->first();
        if($exchange) {
            return [
                'sale' => $exchange->sale
            ];
        }
        $form_params = [
            'fecha' => $date,
        ];

        $this->parameters['form_params'] = $form_params;
        $res = $this->client->request('POST', '/api/tipo_de_cambio', $this->parameters);
        $response = json_decode($res->getBody()->getContents(), true);

        if($response['success']) {
            $data = $response['data'];
            ExchangeRate::query()->create([
                'date' => $data['fecha_busqueda'],
                'date_original' => $data['fecha_sunat'],
                'sale_original' => $data['venta'],
                'sale' => $data['venta'],
                'purchase_original' => $data['compra'],
                'purchase' => $data['compra'],
            ]);

            return [
                'sale' => $data['venta']
            ];
        }
        return [
            'sale' => 1,
        ];
    }
}
