<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CredentialController extends Controller
{
    public function index()
    {
        $credentials = \App\Models\Credential::all();
        return view('credentials.index', compact('credentials'));
    }

    public function create()
    {
        return view('credentials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'password' => 'required|string',
        ]);

        if (empty($request->username) && empty($request->email)) {
            return back()->withInput()->withErrors(['email' => 'Either username or email must be provided']);
        }

        \App\Models\Credential::create($request->all());

        return redirect()->route('credentials.index')->with('success', 'Credential created successfully.');
    }

    public function edit(\App\Models\Credential $credential)
    {
        return view('credentials.edit', compact('credential'));
    }

    public function update(Request $request, \App\Models\Credential $credential)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'password' => 'required|string',
        ]);

        if (empty($request->username) && empty($request->email)) {
            return back()->withInput()->withErrors(['email' => 'Either username or email must be provided']);
        }

        $credential->update($request->all());

        return redirect()->route('credentials.index')->with('success', 'Credential updated successfully.');
    }

    public function destroy(\App\Models\Credential $credential)
    {
        $credential->delete();
        return redirect()->route('credentials.index')->with('success', 'Credential deleted successfully.');
    }

    public function exportExcel()
    {
        $credentials = clone \App\Models\Credential::select('id', 'name', 'username', 'email', 'password')->get();
        // Native PHP CSV stream download array
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=credentials.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        $columns = ['ID', 'Name', 'Username', 'Email', 'Password'];

        $callback = function() use($credentials, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($credentials as $cred) {
                fputcsv($file, [
                    $cred->id,
                    $cred->name,
                    $cred->username,
                    $cred->email,
                    $cred->password,
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $credentials = clone \App\Models\Credential::select('id', 'name', 'username', 'email', 'password')->get();
        $pdf = app('dompdf.wrapper')->loadView('credentials.pdf', compact('credentials'));
        return $pdf->download('credentials.pdf');
    }
}
