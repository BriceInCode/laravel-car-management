<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Enums\PermissionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ActivityLogController extends Controller
{
    /**
     * Afficher une liste des logs d'activité.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $logs = ActivityLog::all();
        return view('activity_logs.index', compact('logs'));
    }

    /**
     * Afficher le formulaire de création d'un nouveau log d'activité.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('activity_logs.create');
    }

    /**
     * Stocker un nouveau log d'activité dans la base de données.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();

        try {
            ActivityLog::createLog(Auth::user(), PermissionType::from($data['action']), $data['description']);
            return redirect()->route('activity_logs.index')->with('success', 'Log d\'activité créé avec succès.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Afficher un log d'activité spécifique.
     *
     * @param \App\Models\ActivityLog $activityLog
     * @return \Illuminate\View\View
     */
    public function show(ActivityLog $activityLog)
    {
        return view('activity_logs.show', compact('activityLog'));
    }

    /**
     * Afficher le formulaire d'édition d'un log d'activité.
     *
     * @param \App\Models\ActivityLog $activityLog
     * @return \Illuminate\View\View
     */
    public function edit(ActivityLog $activityLog)
    {
        return view('activity_logs.edit', compact('activityLog'));
    }

    /**
     * Mettre à jour un log d'activité dans la base de données.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ActivityLog $activityLog
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ActivityLog $activityLog)
    {
        $data = $request->all();
        $data['user_id'] = Auth::id();

        $validation = ActivityLog::validate($data);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }

        $activityLog->update($data);
        return redirect()->route('activity_logs.index')->with('success', 'Log d\'activité mis à jour avec succès.');
    }

    /**
     * Supprimer un log d'activité de la base de données.
     *
     * @param \App\Models\ActivityLog $activityLog
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();
        return redirect()->route('activity_logs.index')->with('success', 'Log d\'activité supprimé avec succès.');
    }
}
