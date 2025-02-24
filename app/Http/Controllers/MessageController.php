<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    /**
     * Afficher une liste des messages.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $messages = Message::all();
        return view('messages.index', compact('messages'));
    }

    /**
     * Afficher le formulaire de création d'un nouveau message.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $users = User::all(); // Récupérer tous les utilisateurs pour les options d'expéditeur et de destinataire
        return view('messages.create', compact('users'));
    }

    /**
     * Stocker un nouveau message dans la base de données.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['sender_id'] = Auth::id(); // L'utilisateur connecté est l'expéditeur

        try {
            Message::sendMessage(Auth::user(), User::find($data['receiver_id']), $data['content']);
            return redirect()->route('messages.index')->with('success', 'Message envoyé avec succès.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Afficher un message spécifique.
     *
     * @param \App\Models\Message $message
     * @return \Illuminate\View\View
     */
    public function show(Message $message)
    {
        return view('messages.show', compact('message'));
    }

    /**
     * Afficher le formulaire d'édition d'un message.
     *
     * @param \App\Models\Message $message
     * @return \Illuminate\View\View
     */
    public function edit(Message $message)
    {
        $users = User::all(); // Récupérer tous les utilisateurs pour les options d'expéditeur et de destinataire
        return view('messages.edit', compact('message', 'users'));
    }

    /**
     * Mettre à jour un message dans la base de données.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Message $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Message $message)
    {
        $data = $request->all();
        $data['sender_id'] = Auth::id(); // L'utilisateur connecté est l'expéditeur

        $validation = Message::validate($data);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }

        $message->update($data);
        return redirect()->route('messages.index')->with('success', 'Message mis à jour avec succès.');
    }

    /**
     * Supprimer un message de la base de données.
     *
     * @param \App\Models\Message $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Message $message)
    {
        $message->delete();
        return redirect()->route('messages.index')->with('success', 'Message supprimé avec succès.');
    }
}
