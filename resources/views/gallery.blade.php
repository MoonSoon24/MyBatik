<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Batik - Gallery</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                        'dancing': ['Dancing Script', 'cursive'],
                    }
                }
            }
        }
    </script>
    <style>
        .font-dancing { font-family: 'Dancing Script', cursive; }
        html { scroll-behavior: smooth; }
        .modal-body { max-height: 90vh; }
        .comments-container::-webkit-scrollbar { width: 8px; }
        .comments-container::-webkit-scrollbar-track { background: #f1f1f1; }
        .comments-container::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        .comments-container::-webkit-scrollbar-thumb:hover { background: #555; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

    <x-header />
    
    <main class="container mx-auto px-4 py-8" x-data="galleryPage()">
        <h1 class="text-3xl md:text-4xl font-bold text-center mb-8">Community Gallery</h1>
        @if($designs->isEmpty())
            <div class="text-center py-16"><p class="text-xl text-gray-500">No designs have been shared yet. Be the first!</p></div>
        @else
            {{-- CHANGE: Adjusted grid gaps for better responsive spacing --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 md:gap-4">
                @foreach($designs as $design)
                <div id="design-card-{{ $design->id }}" @click="openDetailModal({{ json_encode($design) }})" class="group relative aspect-square cursor-pointer bg-gray-200 rounded-md overflow-hidden">
                    {{-- CHANGE: Switched to object-cover for a cleaner look, added placeholder --}}
                    <img src="{{ asset('storage/' . $design->image_path) }}" alt="{{ $design->title }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/400x400/eeeeee/222222?text=Batik';">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center space-x-6 text-white">
                            <div class="flex items-center space-x-2">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                <span class="font-bold like-count">{{ $design->likes_count ?? $design->likes->count() }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M21.99 4c0-1.1-.89-2-1.99-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14l4 4-.01-18z"/></svg>
                                <span class="font-bold">{{ $design->comments_count ?? $design->comments->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-12">{{ $designs->links() }}</div>
        @endif

        {{-- Detail Modal --}}
        <div x-show="showDetailModal" @keydown.escape.window="showDetailModal = false" x-cloak class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 p-2 sm:p-4">
            {{-- CHANGE: Modal now uses flex-col on mobile and flex-row on desktop --}}
            <div @click.away="showDetailModal = false" class="bg-white w-full max-w-5xl h-full max-h-[95vh] flex flex-col md:flex-row rounded-lg overflow-hidden">

                {{-- Image Section --}}
                <div class="w-full md:w-1/2 bg-gray-100 flex items-center justify-center p-2 h-1/3 md:h-full">
                    <img :src="'/storage/' + currentDesign.image_path" :alt="currentDesign.title" class="max-w-full max-h-full object-contain">
                </div>

                {{-- Details and Comments Section --}}
                <div class="w-full md:w-1/2 flex flex-col h-2/3 md:h-full">
                    <div class="p-4 border-b">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-xl" x-show="!editingTitle" x-text="currentDesign.title"></h3>
                                <div x-show="editingTitle" x-cloak>
                                    <input type="text" x-model="newTitle" class="border rounded px-2 py-1 w-full">
                                    <div class="mt-2">
                                        <button @click="updateTitle" class="text-sm bg-blue-500 text-white py-1 px-2 rounded">Save</button>
                                        <button @click="cancelEditing" class="text-sm bg-gray-300 py-1 px-2 rounded">Cancel</button>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">by <span x-text="currentDesign.user ? currentDesign.user.name : 'Unknown'"></span></p>
                            </div>
                            <div x-show="isOwner" class="flex gap-2" x-cloak>
                                <button @click="startEditing" x-show="!editingTitle" title="Edit Title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 hover:text-blue-600" viewBox="0 0 20 20" fill="currentColor"><path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" /><path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" /></svg>
                                </button>
                                {{-- CHANGE: This button now opens the confirmation modal --}}
                                <button @click="deleteDesign" title="Delete Design">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 hover:text-red-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow p-4 overflow-y-auto comments-container" id="modal-comments-list">
                        {{-- Comments will be injected here by AlpineJS --}}
                    </div>
                    <div class="p-4 border-t bg-gray-50">
                        <div class="flex items-center space-x-4 mb-3">
                            <form @submit.prevent="submitLike" class="like-form-modal">
                                <button type="submit" class="flex items-center space-x-1.5 hover:text-red-500 transition-colors" :class="{'text-red-500': currentUserHasLiked, 'text-gray-500': !currentUserHasLiked}">
                                    <svg class="w-7 h-7" :fill="currentUserHasLiked ? 'currentColor' : 'none'" :stroke="currentUserHasLiked ? 'none' : 'currentColor'" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 20.364l-7.682-7.682a4.5 4.5 0 010-6.364z"></path></svg>
                                    <span class="font-semibold text-lg" x-text="currentDesign.likes_count"></span>
                                </button>
                            </form>
                            <button @click="downloadImage" class="flex items-center space-x-1.5 text-gray-500 hover:text-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                <span class="font-semibold text-lg">Download</span>
                            </button>
                        </div>
                        @auth
                        <form @submit.prevent="submitComment($event)" class="comment-form-modal">
                            <div class="flex items-center space-x-2">
                                <textarea name="body" rows="1" class="w-full border rounded-md px-3 py-2 text-sm resize-none" placeholder="Add a comment..."></textarea>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 font-semibold">Post</button>
                            </div>
                        </form>
                        @else
                        <p class="text-sm text-center text-gray-500"><a href="{{ route('login') }}" class="text-blue-500 hover:underline">Log in</a> to comment.</p>
                        @endauth
                    </div>
                </div>
            </div>
            <button @click="showDetailModal = false" class="absolute top-2 right-2 md:top-4 md:right-4 text-white hover:text-gray-300 text-4xl">&times;</button>
        </div>

        {{-- ADDED: Delete Confirmation Modal --}}
        <div x-show="showDeleteConfirm" x-cloak class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
            <div @click.away="showDeleteConfirm = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm text-center">
                <h3 class="text-xl font-bold mb-4">Are you sure?</h3>
                <p class="text-gray-600 mb-6">This action cannot be undone. The design will be permanently deleted.</p>
                <div class="flex justify-center gap-4">
                    <button @click="showDeleteConfirm = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">Cancel</button>
                    <button @click="executeDelete" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg">Delete</button>
                </div>
            </div>
        </div>
    </main>

    @auth
    <div x-data="{}" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40">
        {{-- CHANGE: Responsive button size --}}
        <button @click="$dispatch('open-modal', 'upload-design')" class="bg-black text-white font-semibold py-3 px-6 sm:py-4 sm:px-8 rounded-full shadow-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-black focus:ring-opacity-50 transition-all duration-300 text-base sm:text-lg flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
            Upload Design
        </button>
    </div>
    @endauth
    
    {{-- Upload Modal --}}
    <div x-data="{ show: false }" @open-modal.window="if ($event.detail === 'upload-design') show = true" x-show="show" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40 p-4">
        <div @click.away="show = false" class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md modal-body overflow-y-auto">
            <h3 class="text-2xl font-bold mb-6 text-center">Share Your Design</h3>
            <form action="{{ route('designs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Design Title</label>
                        <input type="text" name="title" id="title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., 'Ocean Waves Shirt'" required>
                    </div>
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">Upload Image</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload a file</span>
                                        <input id="image-upload" name="image" type="file" class="sr-only" required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-4">
                    <button type="button" @click="show = false" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg">Cancel</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">Share</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
        @if (session('success'))
            window.dispatchEvent(new CustomEvent('alert', { 
                detail: { type: 'success', message: "{{ session('success') }}" }
            }));
        @endif

        @if ($errors->any())
            window.dispatchEvent(new CustomEvent('alert', {
                    detail: { type: 'error', message: "{{ $errors->first() }}" }
            }));
        @endif
    });
    
    function galleryPage() {
        return {
            showDetailModal: false,
            showDeleteConfirm: false, // ADDED for delete confirmation
            currentDesign: {},
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            authUserId: {{ auth()->id() ?? 'null' }},
            currentUserHasLiked: false,
            isOwner: false,
            editingTitle: false,
            newTitle: '',

            openDetailModal(design) {
                this.currentDesign = design;
                if (typeof this.currentDesign.likes_count === 'undefined') {
                    this.currentDesign.likes_count = this.currentDesign.likes.length;
                }
                
                this.currentUserHasLiked = this.isLikedByUser();
                this.isOwner = this.authUserId && this.currentDesign.user_id == this.authUserId;
                
                this.editingTitle = false;
                this.showDetailModal = true;
                
                this.$nextTick(() => {
                    const commentsList = document.getElementById('modal-comments-list');
                    commentsList.innerHTML = ''; 
                    if (this.currentDesign.comments && this.currentDesign.comments.length > 0) {
                        this.currentDesign.comments.forEach(comment => {
                            commentsList.innerHTML += this.createCommentHtml(comment);
                        });
                    } else {
                        commentsList.innerHTML = '<p class="text-sm text-gray-500 no-comments-placeholder">No comments yet.</p>';
                    }
                });
            },

            isLikedByUser() {
                if (!this.currentDesign.likes || !this.authUserId) return false;
                return this.currentDesign.likes.some(like => like.user_id == this.authUserId);
            },

            submitLike() {
                fetch(`/designs/${this.currentDesign.id}/like`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                })
                .then(res => {
                    if (!res.ok) {
                        if (res.status === 401) window.location.href = '{{ route("login") }}';
                        return Promise.reject(res);
                    }
                    return res.json();
                })
                .then(data => {
                    this.currentUserHasLiked = data.liked;
                    this.currentDesign.likes_count = data.likes_count;
                    if (data.liked) {
                        if (!this.currentDesign.likes.some(like => like.user_id == this.authUserId)) {
                            this.currentDesign.likes.push({ user_id: this.authUserId });
                        }
                    } else {
                        this.currentDesign.likes = this.currentDesign.likes.filter(like => like.user_id != this.authUserId);
                    }
                    const gridCardLikeCount = document.querySelector(`#design-card-${this.currentDesign.id} .like-count`);
                    if (gridCardLikeCount) {
                        gridCardLikeCount.innerText = data.likes_count;
                    }
                })
                .catch(error => console.error('Error:', error));
            },

            downloadImage() {
                fetch('/storage/' + this.currentDesign.image_path)
                    .then(response => response.blob())
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        a.download = 'mybatik-gallery.jpg';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        a.remove();
                    })
                    .catch(() => alert('Could not download image.'));
            },

            startEditing() {
                this.newTitle = this.currentDesign.title;
                this.editingTitle = true;
            },
            cancelEditing() {
                this.editingTitle = false;
            },
            updateTitle() {
                fetch(`/designs/${this.currentDesign.id}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ title: this.newTitle })
                })
                .then(res => {
                    if (!res.ok) return Promise.reject(res);
                    return res.json();
                })
                .then(data => {
                    this.currentDesign.title = data.title;
                    this.editingTitle = false;
                })
                .catch(err => console.error('Update failed:', err));
            },
            
            // CHANGE: This now opens the confirmation modal instead of deleting directly
            deleteDesign() {
                this.showDeleteConfirm = true;
            },
            
            // ADDED: This function contains the actual delete logic
            executeDelete() {
                fetch(`/designs/${this.currentDesign.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                })
                .then(res => {
                    if (!res.ok) return Promise.reject(res);
                    return res.json();
                })
                .then(data => {
                    document.getElementById(`design-card-${this.currentDesign.id}`).remove();
                    this.showDeleteConfirm = false;
                    this.showDetailModal = false;
                    window.dispatchEvent(new CustomEvent('alert', { 
                        detail: { type: 'success', message: 'Design deleted successfully.' }
                    }));
                })
                .catch(err => {
                    console.error('Delete failed:', err)
                    this.showDeleteConfirm = false;
                    window.dispatchEvent(new CustomEvent('alert', { 
                        detail: { type: 'error', message: 'Failed to delete design.' }
                    }));
                });
            },

            submitComment(event) {
                const form = event.target;
                const formData = new FormData(form);
                const textarea = form.querySelector('textarea');

                fetch(`/designs/${this.currentDesign.id}/comments`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                    body: formData
                })
                .then(res => {
                    if (!res.ok) {
                        if (res.status === 401) window.location.href = '{{ route("login") }}';
                        return Promise.reject(res);
                    }
                    return res.json();
                })
                .then(data => {
                    this.currentDesign.comments.unshift(data.comment);
                    const commentsList = document.getElementById('modal-comments-list');
                    const noCommentsPlaceholder = commentsList.querySelector('.no-comments-placeholder');
                    if (noCommentsPlaceholder) noCommentsPlaceholder.remove();
                    commentsList.insertAdjacentHTML('afterbegin', this.createCommentHtml(data.comment));
                    textarea.value = '';
                })
                .catch(error => console.error('Error:', error));
            },

            createCommentHtml(comment) {
                const date = new Date(comment.created_at);
                const formattedDate = date.toLocaleString('en-US', { month: 'short', day: 'numeric' });
                const userName = comment.user ? comment.user.name : 'A User';
                return `
                    <div class="text-sm flex items-start space-x-3 mb-3">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold">${userName.charAt(0)}</div>
                        <div>
                            <p><strong class="font-semibold">${userName}:</strong> ${comment.body}</p>
                            <p class="text-xs text-gray-400 mt-1">${formattedDate}</p>
                        </div>
                    </div>
                `;
            }
        }
    }
    
    </script>
</body>
</html>
