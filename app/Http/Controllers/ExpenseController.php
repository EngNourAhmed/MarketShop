<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExpenseResponse;
use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::with('user')->latest()->paginate(10);
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(ExpenseResponse::collection($expenses));
        }

        return view('expense.index', [
            'expenses' => $expenses,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description'  => 'required|string|max:255',
            'type'         => 'required|string|max:255',
            'category'     => 'required|string|max:255',
            'amount'       => 'required|numeric',
            'expense_date' => 'required|date',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // إذا كان هناك صورة، حفظها في المسار المناسب
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('expenses', 'public');
        }

        // إنشاء الكائن Expense
        $expense = new Expense();
        $expense->fill($validated);

        // تعيين user_id استنادًا إلى المستخدم المتصل
        if (auth()->check()) {
            $expense->user_id = auth()->id();
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // حفظ السجل
        $expense->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($expense, 201);
        }

        return redirect()
            ->route('expenses.index')
            ->with('status', 'تم إضافة المصروف بنجاح');
    }


    public function getCurrentUser()
    {
        // الحصول على اليوزر الحالي من الـ auth
        $user = Auth::user();

        // التحقق إذا كان هناك يوزر تم تسجيل دخوله
        if ($user) {
            return response()->json([
                'user' => $user,
            ], 200);
        }

        // إذا لم يكن هناك يوزر مسجل دخول
        return response()->json([
            'message' => 'لا يوجد يوزر مسجل دخول',
        ], 401);
    }


    public function show($id)
    {
        $expense = Expense::with('user')->findOrFail($id);
        return response()->json($expense);
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Expense deleted successfully']);
        }

        return redirect()
            ->route('expenses.index')
            ->with('status', 'تم حذف المصروف بنجاح');
    }


    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'description'  => 'required|string|max:255',
            'type'         => 'required|string|max:255',
            'category'     => 'required|string|max:255',
            'amount'       => 'required|numeric',
            'expense_date' => 'required|date',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('expenses', 'public');
        } else {
            unset($validated['image']);
        }

        $expense->forceFill($validated);
        $expense->save();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($expense);
        }

        return redirect()
            ->route('expenses.index')
            ->with('status', 'تم تحديث المصروف بنجاح');
    }


    public function print($id)
    {
        $expense = Expense::with('user')->findOrFail($id);
        return response()->json(['message' => 'Print view not implemented', 'data' => $expense]);
    }

    public function pdf($id)
    {
        $expense = Expense::with('user')->findOrFail($id);
        return response()->json(['message' => 'PDF generation not implemented', 'data' => $expense]);
    }

    public function excel($id)
    {
        $expense = Expense::with('user')->findOrFail($id);
        return response()->json(['message' => 'Excel export not implemented', 'data' => $expense]);
    }

    public function csv($id)
    {
        $expense = Expense::with('user')->findOrFail($id);
        return response()->json(['message' => 'CSV export not implemented', 'data' => $expense]);
    }
}
