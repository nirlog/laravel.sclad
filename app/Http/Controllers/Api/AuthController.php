<?php
namespace App\Http\Controllers\Api;
use App\Models\User;use Illuminate\Http\Request;use App\Http\Controllers\Controller;use Illuminate\Support\Facades\Hash;use Illuminate\Validation\ValidationException;
class AuthController extends Controller{public function login(Request $r){$data=$r->validate(['email'=>'required|email','password'=>'required']);$u=User::where('email',$data['email'])->first();if(!$u||!Hash::check($data['password'],$u->password)){throw ValidationException::withMessages(['email'=>'Неверные учетные данные.']);}return ['token'=>$u->createToken('mobile')->plainTextToken,'user'=>$u];} public function logout(Request $r){$r->user()->currentAccessToken()?->delete();return response()->noContent();} public function user(Request $r){return $r->user();}}
