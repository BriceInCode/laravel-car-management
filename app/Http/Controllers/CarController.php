<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    /**
     * Afficher une liste de voitures.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cars = Car::all();
        return view('cars.index', compact('cars'));
    }

    /**
     * Afficher le formulaire de création d'une nouvelle voiture.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('cars.create');
    }

    /**
     * Stocker une nouvelle voiture dans la base de données.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['created_by'] = Auth::id();

        $errors = Car::validateCarData($data);
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        Car::create($data);
        return redirect()->route('cars.index')->with('success', 'Voiture créée avec succès.');
    }

    /**
     * Afficher une voiture spécifique.
     *
     * @param \App\Models\Car $car
     * @return \Illuminate\View\View
     */
    public function show(Car $car)
    {
        return view('cars.show', compact('car'));
    }

    /**
     * Afficher le formulaire d'édition d'une voiture.
     *
     * @param \App\Models\Car $car
     * @return \Illuminate\View\View
     */
    public function edit(Car $car)
    {
        return view('cars.edit', compact('car'));
    }

    /**
     * Mettre à jour une voiture dans la base de données.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Car $car
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Car $car)
    {
        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $errors = Car::validateCarData($data);
        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $car->update($data);
        return redirect()->route('cars.index')->with('success', 'Voiture mise à jour avec succès.');
    }

    /**
     * Supprimer une voiture de la base de données.
     *
     * @param \App\Models\Car $car
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Car $car)
    {
        $car->deleted_by = Auth::id();
        $car->save();
        $car->delete();

        return redirect()->route('cars.index')->with('success', 'Voiture supprimée avec succès.');
    }

    /**
     * Afficher les voitures disponibles.
     *
     * @return \Illuminate\View\View
     */
    public function availableCars()
    {
        $cars = Car::available()->get();
        return view('cars.available', compact('cars'));
    }

    /**
     * Afficher les voitures vendues.
     *
     * @return \Illuminate\View\View
     */
    public function soldCars()
    {
        $cars = Car::sold()->get();
        return view('cars.sold', compact('cars'));
    }
}
