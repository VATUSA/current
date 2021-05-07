<?php

namespace App\Http\Controllers;

use App\Classes\RoleHelper;
use App\Policy;
use App\PolicyCategory;
use Illuminate\Http\Request;

class PolicyController extends Controller
{

    public function __construct()
    {
        $this->middleware('bindings');
        $this->middleware('vatusastaff')->except(['index', 'show']);
    }

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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function storeCategory(Request $request)
    {
        //Create default category

        $last = PolicyCategory::orderByDesc('order')->first();
        $order = $last ? $last->order + 1 : 0;

        $cat = new PolicyCategory();
        $cat->name = "New Category " . ($order + 1);
        $cat->order = $order;
        $cat->save();

        return redirect('/mgt/policies');
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit()
    {
        $categories = PolicyCategory::with('policies')->orderBy('order')->get();

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
     * Update the policy category.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\PolicyCategory      $category
     *
     * @return \Illuminate\Http\Response
     */
    public function updateCategory(Request $request, PolicyCategory $category)
    {
        if ($request->input('name')) {
            $category->name = $request->name;
        }
        if ($request->filled('order')) {
            $category->order = $request->order;
        }

        $category->save();

        echo 1;
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
     * @param \App\PolicyCategory $category
     *
     * @return \Illuminate\Foundation\Application
     * @throws \Exception
     */
    public function destroyCategory(PolicyCategory $category)
    {
        $order = $category->order;
        $category->delete();

        $categories = PolicyCategory::where('order', '>', $order)->orderBy('order')->get();
        foreach ($categories as $category) {
            $category->order--;
            $category->save();
        }

        return redirect('/mgt/policies');
    }


}
