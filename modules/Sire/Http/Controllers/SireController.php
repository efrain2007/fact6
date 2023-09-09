<?php

namespace Modules\Sire\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sire\Helpers\SireService;
use App\Models\Tenant\Company;

class SireController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('sire::index');
    }

    public function tables()
    {
        $sire = new SireService();
        $periods = $sire->getPeriods();
        return $periods;
    }

    /*
     * route post sire/configuration/update
     * view company
     */
    public function updateConfig(Request $request)
    {
        $company = Company::first();
        $company->sire_client_id = $request->sire_client_id;
        $company->sire_client_secret = $request->sire_client_secret;
        $company->sire_username = $request->sire_username;
        $company->sire_password = $request->sire_password;
        $company->save();

        return [
            'success' => true,
            'message' => 'SIRE actualizado correctamente'
        ];
    }

    /*
     * route get sire/configuration
     * view company
     */
    public function getConfig()
    {
        $company = Company::select('sire_client_id','sire_client_secret','sire_username','sire_password')->first();

        return [
            'success' => true,
            'data' => $company,
        ];
    }

    /*
     * route get sire/ticket/{period}
     */
    public function getTicket($period)
    {
        $sire = new SireService();
        return $sire->getTicket($period);
    }

    public function queryTicket(Request $request)
    {
        $sire = new SireService();
        $response = $sire->queryTicket($request->page,$request->period,$request->ticket);
        return $response;
    }
}
