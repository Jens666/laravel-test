<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
	public function store(Request $request) {
		$user = User::firstOrCreate(['email' => $request->email]);
		if (!$user->wasRecentlyCreated) {
			$user->times_returned++;
			$user->save();
		}
	}
}
