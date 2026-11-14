<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::all();

        return view('users', compact('users'));
    }

    public function create(): View
    {
        return view('user-form', ['user' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')],
            'full_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);
        $data['name'] = $data['full_name'];
        User::create($data);

        return to_route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('user-form', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user)],
            'full_name' => ['required', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048', 'dimensions:max_width=4096,max_height=4096'],
        ]);
        unset($data['avatar']);
        $newAvatar = null;
        if ($request->hasFile('avatar')) {
            if (! class_exists(ImageManager::class)) {
                throw ValidationException::withMessages(['avatar' => 'Image processing is awaiting installation of the instructor-approved package.']);
            }
            try {
                $image = (new ImageManager(new Driver))->read($request->file('avatar')->getRealPath())->cover(256, 256)->toPng();
            } catch (Throwable $exception) {
                report($exception);
                throw ValidationException::withMessages(['avatar' => 'This image could not be processed. Please choose another JPG, PNG, WebP, or GIF image.']);
            }
            $newAvatar = 'avatars/'.Str::uuid().'.png';
            if (! Storage::disk('public')->put($newAvatar, (string) $image)) {
                throw ValidationException::withMessages(['avatar' => 'The image could not be saved. Please try again.']);
            }
            $user->avatar = $newAvatar;
        }
        $data['name'] = $data['full_name'];
        try {
            $user->fill($data)->save();
        } catch (Throwable $exception) {
            if ($newAvatar !== null) {
                Storage::disk('public')->delete($newAvatar);
            }
            throw $exception;
        }

        return to_route('users.index')->with('success', 'User updated successfully.');
    }
}
