<?php

namespace App\Http\Controllers;

use App\Models\EmailThread;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InboxController extends Controller
{
    public function index(Request $request): View
    {
        $folder = $request->string('folder')->toString() ?: 'inbox';

        return view('inbox.index', [
            'threads' => $this->threads($folder, $request),
            'folder' => $folder,
            'thread' => null,
            'counts' => $this->counts(),
        ]);
    }

    public function show(Request $request, EmailThread $thread): View
    {
        $thread->update(['is_read' => true]);
        $thread->load(['messages', 'person.organization', 'deal']);

        $folder = $thread->folder;

        return view('inbox.index', [
            'threads' => $this->threads($folder, $request),
            'folder' => $folder,
            'thread' => $thread,
            'counts' => $this->counts(),
        ]);
    }

    public function reply(Request $request, EmailThread $thread): RedirectResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:10000']]);

        $user = $request->user();
        $to = $thread->messages()->where('direction', 'incoming')->latest('sent_at')->value('from_email')
            ?? $thread->person?->email
            ?? 'contact@example.com';

        $thread->messages()->create([
            'direction' => 'outgoing',
            'from_name' => $user->name,
            'from_email' => $user->email,
            'to_email' => $to,
            'body' => $data['body'],
            'sent_at' => now(),
        ]);

        $thread->update(['last_message_at' => now(), 'is_read' => true]);

        return back()->with('status', 'Reply sent.');
    }

    public function star(EmailThread $thread): RedirectResponse
    {
        $thread->update(['is_starred' => ! $thread->is_starred]);

        return back();
    }

    public function archive(EmailThread $thread): RedirectResponse
    {
        $thread->update(['folder' => $thread->folder === 'archived' ? 'inbox' : 'archived']);

        return redirect()->route('inbox.index')->with('status', 'Conversation moved.');
    }

    private function threads(string $folder, Request $request)
    {
        return EmailThread::query()
            ->with(['person', 'latestMessage', 'deal'])
            ->when($folder === 'starred', fn ($query) => $query->where('is_starred', true))
            ->when($folder !== 'starred', fn ($query) => $query->where('folder', $folder))
            ->when($request->filled('q'), fn ($query) => $query->whereLike('subject', '%'.$request->string('q')->toString().'%'))
            ->orderByDesc('last_message_at')
            ->get();
    }

    /**
     * @return array<string, int>
     */
    private function counts(): array
    {
        return [
            'inbox' => EmailThread::where('folder', 'inbox')->count(),
            'unread' => EmailThread::where('folder', 'inbox')->where('is_read', false)->count(),
            'sent' => EmailThread::where('folder', 'sent')->count(),
            'starred' => EmailThread::where('is_starred', true)->count(),
            'archived' => EmailThread::where('folder', 'archived')->count(),
        ];
    }
}
