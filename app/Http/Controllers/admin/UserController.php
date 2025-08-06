<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Promo;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;


class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $orders = Order::all();
        $promos = Promo::latest()->get();
        $notifications = Notification::with('user')->latest()->get();

        return view('admin.home', compact('orders','users','promos','notifications'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validatedData);

        return response()->json(['message' => 'User updated successfully!']);
    }

    public function verify(string $id)
    {
        $user = User::findOrFail($id);
        
        try {
            $user->email_verified_at = $user->email_verified_at ? null : now();
            $user->save();
            return response()->json(['message' => 'User verification status changed!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to change verification status.'], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete user.'], 500);
        }
    }
}
