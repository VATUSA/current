<?php

namespace App\Http\Controllers;

use App\Classes\RoleHelper;
use App\Policy;
use App\PolicyCategory;
use Auth;
use Composer\Util\AuthHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

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
     * @return \Illuminate\Http\Response|string
     */
    public function store(Request $request)
    {
        $request->validate([
            'category'  => 'required|exists:policy_categories,id',
            'ident'     => 'required|regex:/^[\s\w.]*$/|max:10',
            'title'     => 'required|unique:policies',
            'slug'      => 'required|unique:policies|alpha_dash',
            'perms'     => 'required',
            'file'      => 'required|file|max:1000000',
            'effective' => 'date_format:m/d/Y',
            'desc'      => 'max:255'
        ]);

        $prevPolicy = Policy::where('category', $request->category)->orderByDesc('order')->first();
        $order = $prevPolicy ? $prevPolicy->order + 1 : 0;

        $policy = new Policy();
        $policy->ident = $request->ident;
        $policy->category = $request->category;
        $policy->title = $request->title;
        $policy->slug = strtolower($request->slug);
        $policy->description = $request->desc;
        $policy->extension = $request->file('file')->getClientOriginalExtension();
        $policy->effective_date = Carbon::createFromFormat('m/d/Y', $request->effective)->format('Y-m-d');
        $policy->order = $order;
        $policy->perms = implode('|', $request->perms);
        $policy->visible = false;
        $policy->save();

        if (!$request->file('file')->storeAs('docs',
            $policy->slug . "." . $request->file('file')->getClientOriginalExtension(),
            'public')) {
            try {
                $policy->delete();
            } catch (\Exception $e) {
            }

            return "0";
        } else {
            return "1";
        }

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
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(string $slug)
    {
        $policy = Policy::where('slug', $slug)->first();
        if (!RoleHelper::canView($policy)) {
            abort(403, "You are not allowed to access that file.");
        }
        if ($policy && Storage::disk('public')->exists("docs/$policy->slug.$policy->extension")) {
            return response()->file(Storage::disk('public')->path("docs/$policy->slug.$policy->extension"));
        }

        abort(404);
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
     * @return \Illuminate\Http\Response|string
     */
    public function update(Request $request, Policy $policy)
    {
        if ($request->has('order')) {
            $policy->timestamps = false;
            $policy->order = $request->order;
            $policy->save();

            return "1";
        }
        if ($request->has('visible')) {
            $policy->visible = $request->visible === "true";
            $policy->save();

            return "1";
        }

        $request->validate([
            'category_edit'  => 'required|exists:policy_categories,id',
            'ident'     => 'required|regex:/^[\s\w.]*$/|max:10',
            'title'     => 'required',
            //   'slug'        => 'required|unique:policies|alpha_dash',
            'perms'     => 'required',
            'file'      => 'max:1000000',
            'effective' => 'date_format:m/d/Y',
            'desc'      => 'max:255'
        ]);

        $policy->ident = $request->ident;

        if ($policy->category !== $request->category_edit) {
            $policies = Policy::where('category', $request->category_edit)->orderByDesc('order')->first();
            $policy->order = $policies ? $policies->order + 1 : 0;
        }
        $policy->category = $request->category_edit;

        $policy->title = $request->title;
        // $policy->slug = strtolower($request->slug);
        $policy->description = $request->desc;
        $oldfileextension = $policy->extension;
        $policy->extension = $request->file !== "undefined" ? $request->file('file')->getClientOriginalExtension() : $policy->extension;
        $policy->effective_date = Carbon::createFromFormat('m/d/Y', $request->effective)->format('Y-m-d');
        $policy->perms = implode('|', $request->perms);
        $policy->save();

        if ($request->file !== "undefined") {
            if (!Storage::disk('public')->delete('docs/' . $policy->slug . "." . $oldfileextension)) {
                $policy->extension = $oldfileextension;
                $policy->save();

                return "0";
            }

            if (!$request->file('file')->storeAs('docs',
                $policy->slug . "." . $request->file('file')->getClientOriginalExtension(),
                'public')) {
                return "0";
            } else {
                return "1";
            }
        }

        return "1";
    }

    /**
     * Update the policy category.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\PolicyCategory      $category
     *
     * @return \Illuminate\Http\Response
     */
    public
    function updateCategory(
        Request $request,
        PolicyCategory $category
    ) {
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
     * @return \Illuminate\Http\Response|string
     */
    public
    function destroy(
        Policy $policy
    ) {
        if (Storage::disk('public')->delete('docs/' . $policy->slug . "." . $policy->extension)) {
            try {
                $policy->delete();
            } catch (\Exception $e) {
                return $e->getMessage();
            }

            return "1";
        }

        return "0";
    }

    /**
     * Remove the policy.
     *
     * @param \App\PolicyCategory $category
     *
     * @return \Illuminate\Foundation\Application
     * @throws \Exception
     */
    public
    function destroyCategory(
        PolicyCategory $category
    ) {
        $order = $category->order;
        $category->delete();

        $categories = PolicyCategory::where('order', '>', $order)->orderBy('order')->get();
        foreach ($categories as $category) {
            $category->order--;
            $category->save();
        }

        return redirect('/mgt/policies');
    }

    public
    function getPolicy(
        Request $request,
        Policy $policy
    ) {
        if (!$request->ajax()) {
            abort(400);
        }

        return response()->json($policy->toArray());
    }


}
