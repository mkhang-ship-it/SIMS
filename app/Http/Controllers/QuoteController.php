<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quote;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::orderBy('created_at', 'desc')->get();
        return view('quotes.index', compact('quotes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quote' => 'required|string|max:1000',
            'author' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive',
        ]);

        $quote = Quote::create([
            'quote' => $request->quote,
            'author' => $request->author,
            'status' => $request->status,
        ]);

        return redirect()->route('backend.quotes.index')->with('success', 'Quote đã được tạo thành công!');
    }

    public function update(Request $request, Quote $quote)
    {
        $request->validate([
            'quote' => 'required|string|max:1000',
            'author' => 'nullable|string|max:255',
            'status' => 'required|string|in:active,inactive',
        ]);

        $quote->update([
            'quote' => $request->quote,
            'author' => $request->author,
            'status' => $request->status,
        ]);

        return redirect()->route('backend.quotes.index')->with('success', 'Quote đã được cập nhật thành công!');
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();

        return redirect()->route('backend.quotes.index')->with('success', 'Quote đã được xóa thành công!');
    }
}
