<?php

namespace App\Http\Controllers;

use App\Models\TripInvite;
use Illuminate\Http\RedirectResponse;

class TripInviteController extends Controller
{
    public function accept(string $token): RedirectResponse
    {
        $invite = TripInvite::query()
            ->where('token', $token)
            ->firstOrFail();

        if ($invite->accepted_at) {
            return redirect('/admin/trips/' . $invite->trip_id . '/edit')
                ->with('status', 'Invite already accepted.');
        }

        if ($invite->is_expired) {
            return redirect('/admin')
                ->with('error', 'Invite link has expired.');
        }

        $user = auth()->user();

        $invite->trip->collaborators()->syncWithoutDetaching([
            $user->id => ['role' => $invite->role ?? 'editor'],
        ]);

        $invite->update([
            'accepted_by' => $user->id,
            'accepted_at' => now(),
        ]);

        return redirect('/admin/trips/' . $invite->trip_id . '/edit')
            ->with('status', 'You now have access to this trip.');
    }
}
