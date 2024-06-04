@extends('layouts.main')

@section('title', 'List URLs')

@section('content')
    <div x-data="urlList()" x-init="fetchUrls()">
        <form @submit.prevent="fetchUrls" class="mb-4">
            <input type="text" x-model="search" placeholder="Search URLs" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2">Search</button>
        </form>
        <table class="min-w-full bg-white">
            <thead>
            <tr>
                <th class="py-2 px-4 border-b">#</th>
                <th class="py-2 px-4 border-b">URL</th>
                <th class="py-2 px-4 border-b">Domain</th>
            </tr>
            </thead>
            <tbody>
            <template x-for="url in urls" :key="url.id">
                <tr>
                    <td class="py-2 px-4 border-b" x-text=""></td>
                    <td class="py-2 px-4 border-b" x-text="url.url"></td>
                    <td class="py-2 px-4 border-b" x-text="url.domain.name"></td>
                </tr>
            </template>
            </tbody>
        </table>
        <div x-html="pagination"></div>
    </div>

    <script>
        function urlList() {
            return {
                search: '',
                urls: [],
                pagination: '',
                fetchUrls() {
                    fetch(`{{ route('urls.index')}}?search=${this.search}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest' // Ensure this header is set
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            this.urls = data.urls.data;
                            this.pagination = data.pagination;
                        })
                        .catch(error => {
                            console.error('There was a problem with the fetch operation:', error);
                        });
                }
            };
        }

    </script>
@endsection
