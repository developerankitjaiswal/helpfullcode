<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Lead;
use App\Models\User;
use App\Helpers\Log;

class LeadController extends Controller
{
    /**
     * create a new instance of the class
     */
    function __construct()
    {
         $this->middleware('permission:lead-list|lead-create|lead-edit|lead-delete', ['only' => ['index', 'show']]);
         $this->middleware('permission:lead-create', ['only' => ['create', 'store']]);
         $this->middleware('permission:lead-edit', ['only' => ['edit', 'update']]);
         $this->middleware('permission:lead-delete', ['only' => ['destroy']]);
    }
    
    /** Display a listing of the resource. **/
    public function index(Request $request)
    {
        if (Auth::check()) { 
            $loginID = Auth::id(); //current login user id
            $user = User::find($loginID); //detail of the current login user 
            $roleId = $user->roles->first()->id; //get current user role
        
            // Check if the user's role ID is 1 or 2
            if ($roleId == 1 || $roleId == 2) {
                $result['data'] = Lead::select('leads.*', 'users.name as username')
                    ->leftJoin('users', 'users.id', '=', 'leads.user_id')
                    ->latest('leads.id')
                    ->get();
            } else {
                $result['data'] = Lead::select('leads.*', 'users.name as username')
                    ->where('user_id', $loginID)
                    ->leftJoin('users', 'users.id', '=', 'leads.user_id')
                    ->latest('leads.id')
                    ->get();
            }
            $result['filterbox'] = session('filter_box', false);
            return view('leads.index', $result);  //redirect to page
        } else {
            return view('auth.login');
        }     
    }

    //Show/Hide Filter Box
    public function filterbox()
    {
        session()->put('filter_box', !session('filter_box'));
        return redirect()->back();
    }

    //Lead Detail
    public function show($id)
    {
        $leadID = decode_string($id);
        $leadrec = Lead::find($leadID);
        return view('leads.detail', compact('leadrec'));
    }

    //create lead
    public function create()
    {
        return view('leads.create');
    }

    /** Store a newly created resource in storage.**/
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'firstname' => ['required', 'regex:/^[a-zA-Z]+$/'],
            'lastname' => 'max:255',
            'mobileno' => 'required|numeric|digits_between:10,12',
            'email' => 'required|email|unique:leads,email',
            'leadsource' => 'required',
            'city' => 'required',
            'leadstatus' => 'required',
            'description' => 'max:500'
        ],
        [
            'firstname.required' => 'First name is required.',
            'mobileno.required' => 'Mobile Number is required',
            'mobileno.numeric' => 'Mobile Number is not valid',
            'mobileno.digits_between' => 'Mobile Number is must be 10 digit',
            'email.required' => 'Email is required',
            'email.email' => 'Please Enter a Valid Email Address',
            'email.unique' => 'Email ID Already Exist',
            'city.required' => 'City Name is required',
            'leadsource.required' => 'Please Select Lead Source',
            'leadstatus.required' => 'Please Select Lead Status',
            'description.max' => 'Description Show be not more than 500 words',
        ]);
        if ($validator->passes()){
            $btn_type = $request->btnsubmit;
            $lead = new Lead;
            $lead->first_name = $request->firstname;
            $lead->last_name = $request->lastname;
            $lead->mobile = $request->mobileno;
            $lead->alt_mobile = $request->altmobile;
            $lead->email = $request->email;
            $lead->city = $request->city;
            $lead->state = $request->state;
            $lead->country = $request->country;
            $lead->description = $request->description;
            $lead->lead_source	 = $request->leadsource;
            $lead->lead_status = $request->leadstatus;
            $lead->user_id = $request->created_by;
            $lead->save();
            //SAVE LOGS
            $logInstance = new Log();
            $logInstance->addToLog('Create', 'New Lead Created', $lead);
            //SAVE LOGS
            if($btn_type=='saveandnew'){
                return redirect()->route('leads.create')->with('success','Lead has been created successfully.');
            } else {
                return redirect()->route('leads.index')->with('success','Lead has been created successfully.');
            }
        } else {
            return redirect()->route('leads.create')->withErrors($validator)->withInput();
        }
    }

}
