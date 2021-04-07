<?php

namespace App\Http\Controllers;

use App\Classes\RoleHelper;
use App\Policy;
use App\PolicyCategory;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $categories = PolicyCategory::with('policies')->get();

        return view('info.policies', compact('categories'));
    }

    /**
     * Store a newly created policy in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Store a newly created policy category in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function storeCategory(Request $request)
    {
        if(RoleHelper::isVATUSAStaff()) {
            //Create default category
        }
    }

    /**
     * Show policy.
     *
     * @param \App\Policy $policy
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Policy $policy)
    {
        //
    }

    /**
     * Show the form for managing policies.
     *
     * @param \App\Policy $policy
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Policy $policy)
    {
        $categories = PolicyCategory::with('policies')->get();

        return view('mgt.policies', compact('categories'));
    }

    /**
     * Update the policy.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Policy              $policy
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Policy $policy)
    {
        //
    }

    /**
     * Remove the policy.
     *
     * @param \App\Policy $policy
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Policy $policy)
    {
        //
    }

    /**
     * Remove the policy.
     *
     * @param \App\Policy $policy
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyCategory(Policy $policy)
    {
        //Remove category and all policies
    }
}
