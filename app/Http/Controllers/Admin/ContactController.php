<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query()
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $contacts = $query->paginate(15);

        // Stats for dashboard
        $stats = [
            'total' => Contact::count(),
            'pending' => Contact::where('status', 'pending')->count(),
            'read' => Contact::where('status', 'read')->count(),
            'replied' => Contact::where('status', 'replied')->count(),
            'closed' => Contact::where('status', 'closed')->count(),
            'today' => Contact::whereDate('created_at', today())->count(),
        ];

        return view('admin.contact.index', compact('contacts', 'stats'));
    }

    public function show(Contact $contact)
    {
        // Mark as read if pending
        if ($contact->status === 'pending') {
            $contact->update(['status' => 'read']);
        }

        return view('admin.contact.show', compact('contact'));
    }

    public function reply(Request $request, Contact $contact)
    {
        $request->validate([
            'admin_reply' => 'required|string|min:10'
        ]);

        $contact->update([
            'admin_reply' => $request->admin_reply,
            'status' => 'replied',
            'replied_at' => now(),
            'replied_by' => Auth::id()
        ]);

        // Send email reply (optional)
        try {
            // Mail::send('emails.contact-reply', [
            //     'contact' => $contact,
            //     'reply' => $request->admin_reply
            // ], function($message) use ($contact) {
            //     $message->to($contact->email, $contact->name)
            //             ->subject('Re: ' . $contact->subject);
            // });
        } catch (\Exception $e) {
            \Log::error('Failed to send contact reply email: ' . $e->getMessage());
        }

        return redirect()->route('admin.contact.show', $contact)
            ->with('success', 'Đã gửi phản hồi thành công!');
    }

    public function updateStatus(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => 'required|in:pending,read,replied,closed'
        ]);

        $contact->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái thành công!');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')
            ->with('success', 'Đã xóa liên hệ thành công!');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,mark_read,mark_replied,mark_closed',
            'contact_ids' => 'required|array',
            'contact_ids.*' => 'exists:contacts,id'
        ]);

        $contacts = Contact::whereIn('id', $request->contact_ids);

        switch ($request->action) {
            case 'delete':
                $contacts->delete();
                $message = 'Đã xóa các liên hệ đã chọn!';
                break;
            case 'mark_read':
                $contacts->update(['status' => 'read']);
                $message = 'Đã đánh dấu đã đọc!';
                break;
            case 'mark_replied':
                $contacts->update(['status' => 'replied']);
                $message = 'Đã đánh dấu đã trả lời!';
                break;
            case 'mark_closed':
                $contacts->update(['status' => 'closed']);
                $message = 'Đã đánh dấu đã đóng!';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}
