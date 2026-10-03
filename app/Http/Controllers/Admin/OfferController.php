<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        $offers = Offer::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.offers.index', compact('offers'));
    }

    public function create(): View
    {
        return view('admin.offers.form', ['offer' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        Offer::create($this->validated($request));

        return redirect()->route('admin.offers.index')->with('status', 'Offer created.');
    }

    public function edit(Offer $offer): View
    {
        return view('admin.offers.form', compact('offer'));
    }

    public function update(Request $request, Offer $offer): RedirectResponse
    {
        $offer->update($this->validated($request));

        return redirect()->route('admin.offers.index')->with('status', 'Offer updated.');
    }

    public function destroy(Offer $offer): RedirectResponse
    {
        $offer->delete();

        return redirect()->route('admin.offers.index')->with('status', 'Offer deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'text' => ['required', 'string', 'max:255'],
            'link_url' => ['nullable', 'string', 'max:255', 'regex:#^(/|https?://)#i'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
        ], [
            'link_url.regex' => 'Link must start with / (e.g. /shop) or http(s)://.',
        ]);
    }
}
