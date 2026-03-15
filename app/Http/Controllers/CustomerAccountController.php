<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerAccountController extends Controller
{
    public function show(Request $request)
    {
        return view('shop.account.show', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:255',
            'birthday' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('users', 'public');
        } else {
            unset($validated['image']);
        }

        $user->forceFill($validated)->save();

        return redirect()
            ->route('shop.account')
            ->with('status', 'تم تحديث البيانات بنجاح');
    }
}
