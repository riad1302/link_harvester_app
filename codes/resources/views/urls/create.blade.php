@extends('layouts.main')

@section('title', 'Create URLs')

@section('content')
    <div x-data="{ urls: '', message: '', messageType: '', errors: [] }">
        <!-- Success/Error Message -->
        <template x-if="message">
            <div :class="messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="mt-4 p-4 rounded relative">
                <p x-text="message"></p>
                <button @click="message = ''" class="absolute top-0 right-0 mt-2 mr-2 text-gray-500 hover:text-gray-700">
                    &times;
                </button>
            </div>
        </template>
        <!-- Validation Errors -->
        <template x-if="errors.length">
            <div class="mt-4 p-4 bg-red-100 text-red-700 rounded relative">
                <ul>
                    <template x-for="error in errors" :key="error">
                        <li x-text="error"></li>
                    </template>
                </ul>
                <button @click="errors = []" class="absolute top-0 right-0 mt-2 mr-2 text-gray-500 hover:text-gray-700">
                    &times;
                </button>
            </div>
        </template>
        <!-- Form -->
        <form @submit.prevent="submitUrls">
            @csrf
            <div class="mb-4">
                <label for="urls" class="block text-gray-700 text-sm font-bold mb-2">Enter URLs (one per line):</label>
                <textarea id="urls" name="urls" x-model="urls" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-64" required></textarea>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Submit
            </button>
        </form>
    </div>

    <script>
        function submitUrls() {
            fetch('{{ route('urls.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ urls: this.urls })
            })
                .then(response => response.json())
                .then(data => {
                    this.message = data.message;
                    this.messageType = data.success ? 'success' : 'error';
                    if (data.success) {
                        this.urls = '';
                        this.errors = [];
                    } else {
                        this.errors = data.errors.urls || [];
                    }
                })
                .catch(() => {
                    this.message = 'There was an error submitting the URLs';
                    this.messageType = 'error';
                    this.errors = [];
                });
        }
    </script>
@endsection
