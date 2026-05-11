<?php

namespace App\Http\Controllers;

use App\Models\UserDashboard;
use App\Http\Requests\StoreUserDashboardControllerRequest;
use App\Http\Requests\UpdateUserDashboardControllerRequest;

class UserDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('dashboard');
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
    public function store(StoreUserDashboardControllerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UserDashboardController $userDashboardController)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserDashboardController $userDashboardController)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserDashboardControllerRequest $request, UserDashboardController $userDashboardController)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserDashboardController $userDashboardController)
    {
        //
    }
}
