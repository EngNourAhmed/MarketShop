<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Advertisement;
use Illuminate\Support\Facades\Auth;

class AdvertisementController extends Controller
{
    public function index()
    {
        $advertisements = Advertisement::with('user')->get();

        $advertisements = $advertisements->map(function ($advertisement) {
            return [
                'id' => $advertisement->id,
                'advertisement_id' => $advertisement->advertisement_id,
                'cost' => $advertisement->cost,
                'discount' => $advertisement->discount,
                'duration' => $advertisement->duration,
                'title' => $advertisement->title,
                'content' => $advertisement->content,
                'link' => $advertisement->link,
                'company' => $advertisement->company,
                'user_id' => $advertisement->user_id,
                'user_name' => $advertisement->user->name,
                'user_email' => $advertisement->user->email,
                'user_phone' => $advertisement->user->phone
            ];
        });

        return response()->json($advertisements);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'advertisement_id' => 'required|integer',
            'cost' => 'required|integer',
            'discount' => 'required|integer',
            'duration' => 'required|string',
            'title' => 'required|string',
            'content' => 'required|string',
            'link' => 'nullable|string',
            'status' => 'required|string',
            'type' => 'required|string',
            'category' => 'nullable|string',
            'sub_category' => 'nullable|string',
        ]);

        
        $validated['image'] = 'sometimes|required|string';
        $advertisement = new Advertisement();
        $advertisement->forceFill($validated);
        $advertisement->user_id = Auth::id();
        $advertisement->save();

        return response()->json($advertisement, 201);
    }

    public function show($id)
    {
        $advertisement = Advertisement::with('user')->findOrFail($id);
        return response()->json($advertisement);
    }

    public function update(Request $request, $id)
    {
        $advertisement = Advertisement::findOrFail($id);

        $validated = $request->validate([
            'advertisement_id' => 'sometimes|required|integer',
            'cost' => 'sometimes|required|integer',
            'discount' => 'sometimes|required|integer',
            'duration' => 'sometimes|required|string',
            'title' => 'sometimes|required|string',
            'content' => 'sometimes|required|string',
            'image' => 'sometimes|required|string',
            'link' => 'sometimes|required|string',
            'status' => 'sometimes|required|string',
            'type' => 'sometimes|required|string',
            'category' => 'sometimes|required|string',
            'sub_category' => 'sometimes|required|string',
        ]);

        $advertisement->forceFill($validated);
        $advertisement->save();

        return response()->json($advertisement);
    }

    public function destroy($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        $advertisement->delete();

        return response()->json(['message' => 'Advertisement deleted successfully']);
    }
}
