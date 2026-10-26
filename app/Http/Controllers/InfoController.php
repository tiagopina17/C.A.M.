<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InfoController extends Controller
{
    /**
     * Display the FAQ and help page.
     */
    public function help(): View
    {
        return view('help');
    }

    /**
     * Display the contact page.
     */
    public function contact(): View
    {
        return view('contact');
    }

    /**
     * Handle incoming contact form submissions.
     */
    public function submitContact(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2500'],
        ], [
            'name.required' => 'Por favor, indique o seu nome.',
            'email.required' => 'Por favor, indique o seu endereço de email.',
            'email.email' => 'Por favor, introduza um email válido.',
            'subject.required' => 'Por favor, escolha o assunto.',
            'message.required' => 'Por favor, escreva a sua mensagem.',
            'message.min' => 'A mensagem deve conter pelo menos 10 caracteres.',
        ]);

        return back()->with('success', 'Mensagem enviada com sucesso! A equipa Cá do Bairro entrará em contacto consigo brevemente.');
    }
}

