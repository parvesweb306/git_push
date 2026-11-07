<?php

namespace App\Http\Controllers;

use App\Models\FamilyInviteLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FamilyInviteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $owners = $user->isAdmin()
            ? $user->subUsers()->get()->push($user)
            : collect([$user]);
        $ownerIds = $owners->pluck('id');
        $links = FamilyInviteLink::whereIn('user_id', $ownerIds)
            ->with('user')
            ->latest()
            ->get();

        return view('family-links.index', [
            'links' => $links,
            'legacyUsers' => $owners->filter(
                fn (User $owner) => !empty($owner->family_invite_code)
            ),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => ['nullable', 'string', 'max:100'],
        ]);

        do {
            $code = strtoupper(Str::random(32));
        } while (
            FamilyInviteLink::where('code', $code)->exists()
            || User::where('family_invite_code', $code)->exists()
        );

        $request->user()->familyInviteLinks()->create([
            'label' => $data['label'] ?: 'Family link',
            'code' => $code,
        ]);

        return redirect()
            ->route('family-links.index')
            ->with('success', 'নতুন family link তৈরি হয়েছে। এখনই ব্যবহার করতে পারবেন।');
    }

    public function destroy(Request $request, FamilyInviteLink $familyInviteLink)
    {
        $this->authorizeOwner($request, $familyInviteLink->user_id);

        $familyInviteLink->delete();

        return redirect()
            ->route('family-links.index')
            ->with('success', 'Family link বাতিল করা হয়েছে। এই link আর কাজ করবে না।');
    }

    public function destroyLegacy(Request $request, User $user)
    {
        $this->authorizeOwner($request, $user->id);

        $user->forceFill([
            'family_invite_code' => null,
        ])->save();

        return redirect()
            ->route('family-links.index')
            ->with('success', 'পুরোনো family link বাতিল করা হয়েছে। এই link আর কাজ করবে না।');
    }

    private function authorizeOwner(Request $request, int $ownerId): void
    {
        $user = $request->user();
        $isOwner = (int) $user->id === $ownerId;
        $isManagedByAdmin = $user->isAdmin()
            && $user->subUsers()->whereKey($ownerId)->exists();

        abort_unless($isOwner || $isManagedByAdmin, 403);
    }
}
