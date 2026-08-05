<?php

namespace App\Livewire\MemberPages\Dashboard;

use App\Models\NewsUpdate;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Wallet;

class NewsUpdates extends Component
{
    use WithFileUploads;


    public $title = '';

    public $details = '';

    public $category = 'General';

    public $image = null;

    public function refreshDashboard()
    {
        session()->flash('success', 'Dashboard data refreshed successfully.');
    }

    public function createPost(): void
    {
        $this->validate([

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'details' => [
                'required',
                'string',
            ],

            'category' => [
                'required',
                'string',
                'max:100',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

        ], [

            'image.image' =>
                'The selected file must be an image.',

            'image.mimes' =>
                'Only JPG, JPEG, PNG, and WEBP images are allowed.',

            'image.max' =>
                'The image must not exceed 5MB.',

        ]);



        /** @var User|null $user */
        $user = Auth::user();


        if (!$user instanceof User) {

            $this->addError(
                'title',
                'You must be logged in to create a post.'
            );

            return;
        }



        $imagePath = null;


        // Store post image
        if ($this->image) {

            $imagePath = $this->image->store(
                'news-images',
                'public'
            );

        }



        NewsUpdate::create([

            'title' => $this->title,

            'details' => $this->details,

            'image' => $imagePath,

            'category' => $this->category,

            'status' => 'Published',

            'UserID' => $user->UserID,

        ]);



        $this->reset([

            'title',

            'details',

            'image',

        ]);


        $this->category = 'General';



        session()->flash(
            'success',
            'News update published successfully.'
        );
    }



    public function deletePost($id): void
    {
        /** @var User|null $user */
        $user = Auth::user();


        if (!$user || (int) $user->RoleID !== 1) {

            return;

        }



        $news = NewsUpdate::find($id);


        if (!$news) {

            return;

        }



        if ($news->image) {

            Storage::disk('public')->delete(
                $news->image
            );

        }



        $news->delete();



        session()->flash(
            'success',
            'News update deleted successfully.'
        );
    }


public function render()
    {
        $newsUpdates = NewsUpdate::with('user')
            ->where('status', 'Published')
            ->latest()
            ->get();

        $userID = Auth::user()->UserID;

        $totalAmount = Wallet::where(
                'UserID',
                $userID
            )
            ->where(
                'status',
                'Active'
            )
            ->sum('amount');

        return view(
            'livewire.member-pages.dashboard.overview',
            [
                'newsUpdates' => $newsUpdates,
                'totalAmount' => $totalAmount,
            ]
        );
    }
}
