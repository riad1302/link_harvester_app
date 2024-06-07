@extends('layouts.main')

@section('title', 'List URLs')

@section('content')
    <div x-data="crudApp()" x-init="fetchItems()">
        <div class="container mx-auto mt-5">
            <div class="mb-4 flex items-center">
                <div class="relative flex-grow">
                    <input type="text" x-model="search" placeholder="Search..." class="border p-2 w-full pr-10" @input="toggleSearchButton">
                    <button x-show="search.trim().length > 0" @click="clearSearch()" class="absolute right-2 top-1/2 transform -translate-y-1/2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9.293l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 011.414-1.414L10 8.586z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <button @click="fetchItems()" class="bg-blue-500 text-white px-4 py-2 ml-2">Search</button>
            </div>

            <table class="min-w-full bg-white">
                <thead>
                <tr>
                    <th class="py-2 px-4 border-b cursor-pointer w-1.5">#</th>
                    <th @click="sortBy('url')" class="py-2 px-4 border-b cursor-pointer w-2/3">URL</th>
                    <th @click="sortBy('domain')" class="py-2 px-4 border-b cursor-pointer">Domain</th>
                </tr>
                </thead>
                <tbody>
                <template x-for="(item, index) in items" :key="item.id">
                    <tr>
                        <td x-text="(currentPage - 1) * 20 + index + 1" class="border px-4 py-2"></td>
                        <td x-text="item.url" class="border px-4 py-2"></td>
                        <td x-text="item.domain.name" class="border px-4 py-2"></td>
                    </tr>
                </template>
                </tbody>
            </table>

            <div x-show="totalRows > 20" class="mt-4 flex items-center">
                <button @click="fetchItems(currentPage - 1)" :disabled="currentPage === 1" class="bg-gray-500 text-white px-4 py-2">Previous</button>
                <span x-text="currentPage" class="px-4 py-2"></span>
                <span>/</span>
                <span x-text="lastPage" class="px-4 py-2"></span>
                <button @click="fetchItems(currentPage + 1)" :disabled="currentPage === lastPage" class="bg-gray-500 text-white px-4 py-2">Next</button>
                <span class="ml-4">Total rows: <span x-text="totalRows"></span></span>
            </div>
        </div>
    </div>

    <script>
        function crudApp() {
            return {
                items: [],
                currentPage: 1,
                lastPage: 1,
                search: '',
                sortColumn: 'url',
                sortDirection: 'asc',
                totalRows: 0,

                fetchItems(page = 1) {
                    const params = new URLSearchParams({
                        page: page,
                        search: this.search,
                        sort: this.sortColumn,
                        direction: this.sortDirection
                    });

                    fetch(`/?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest' // Ensure this header is set
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data)
                            this.items = data.data;
                            this.currentPage = data.current_page;
                            this.lastPage = data.last_page;
                            this.totalRows = data.total;
                        });
                },

                sortBy(column) {
                    this.sortDirection = this.sortColumn === column ? (this.sortDirection === 'asc' ? 'desc' : 'asc') : 'asc';
                    this.sortColumn = column;
                    this.fetchItems();
                },

                clearSearch() {
                    this.search = '';
                    this.fetchItems();
                },

                toggleSearchButton() {
                    // This method is not needed anymore since the visibility is handled by Alpine's x-show
                }
            };
        }
    </script>

@endsection
