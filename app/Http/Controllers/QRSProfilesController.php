<?php
namespace App\Http\Controllers;

use App\Models\AssignmentHistory;
use App\Models\Award;
use App\Models\AwardHistory;
use App\Models\PftHistory;
use App\Models\QRSProfile;
use App\Models\Schooling;
use Illuminate\Http\Request;
use Gate;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use App\Models\Officer;

class QRSProfilesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $config_data;
    public function __construct(QRSProfile $qrsprofile)
    {
        $columnHidden = array_merge($qrsprofile->getDates(), ['id']);
        $columnLabels = [''];    
        $optionalFields = ['name', 'email'];

        $this->config_data = (object) [
            "module_name"=>"QRS Profiles", //Module name
            "module_perm_name"=>"qrsprofile", //Permission name
            "module_route"=>"qrsprofiles", //Web route
            "module_view_folder"=>"officerdata.qrsprofile", //View folder
            "columnHidden"=>$columnHidden,
            "columnLabels"=>$columnLabels,
            "optionalFields"=>$optionalFields,
        ];

        view()->share('config_data', $this->config_data);
    }

    public function profile(Officer $officer)
    {
        $data = $officer->load(['assignments', 'schoolings', 'awards', 'pfts']);

        // return $officer;
        return view($this->config_data->module_view_folder.'.profile', compact('data'));
    }
    public function index()
    {
        abort_if(Gate::denies($this->config_data->module_perm_name.'_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view($this->config_data->module_view_folder.'.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(QRSProfile $qRSProfile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QRSProfile $qRSProfile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QRSProfile $qRSProfile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QRSProfile $qRSProfile)
    {
        //
    }


}
