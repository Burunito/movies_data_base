<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎬 Database</title>
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="max-w-4xl mx-auto mt-8 p-6 bg-white rounded-xl shadow-lg" x-data="app()">
        <div x-show="errorMessage" class="mb-4 p-2 bg-red-100 text-red-700 rounded" x-text="errorMessage"></div>

        <h1 class="text-3xl font-bold mb-6 text-center">🎭 Database</h1>

        <!-- Tabs -->
        <div class="flex justify-center mb-6 space-x-4">
            <button @click="tab='actors'" :class="tab==='actors' ? 'font-bold border-b-2 border-blue-500' : ''">Actors</button>
            <button @click="tab='movies'" :class="tab==='movies' ? 'font-bold border-b-2 border-blue-500' : ''">Movies</button>
            <button @click="tab='links'" :class="tab==='links' ? 'font-bold border-b-2 border-blue-500' : ''">Links</button>
        </div>
        <div class="flex justify-center mb-6 space-x-4">
            <select 
                x-model="selectedGenre" 
                @change="filterByGenre(tab)" 
                class="border p-2 rounded w-full"
            >
                <option value="">Genre</option>
                <template x-for="g in allGenres" :key="g.id">
                    <option :value="g.id" x-text="g.title"></option>
                </template>
            </select>
        </div>
        <!-- Sección Actors -->
        <div x-show="tab==='actors'">
            <h2 class="text-xl font-semibold mb-2">Actors</h2>
            
            <!-- Search / Add -->
            <div class="mb-4 flex gap-2">
                <input type="text" x-model="actorSearch" placeholder="Name actor" class="border p-2 rounded flex-1">
                <input type="text" x-model="actorLink" placeholder="Link" class="border p-2 rounded flex-1">
                <select x-model="selectedGenre" @change="addGenre(selectedGenre)" class="border p-2 rounded w-full">
                    <option value="">Genre</option>
                    <template x-for="g in allGenres" :key="g.id">
                        <option :value="g.id" x-text="g.title"></option>
                    </template>
                </select>
                <div class="mt-2">
                    <input 
                        type="file" 
                        id="actorFile" 
                        @change="handleFileUpload($event)" 
                        class="hidden"
                    >
                    <label 
                        for="actorFile" 
                        class="bg-blue-500 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-600 transition"
                    >
                        ⬆️
                    </label>
                </div>
                <button @click="saveItem('actors')" class="bg-blue-500 text-white px-3 py-1 rounded">
                    Save
                </button>
            </div>
            <div class="flex flex-wrap gap-1 mb-2" x-show="allGenres.length">
                <template x-for="id in actorGenres" :key="id">
                    <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded flex items-center gap-1">
                        <span x-text="allGenres.find(g => g.id == id).title || 'No data'"></span>
                        <button @click="actorGenres = actorGenres.filter(g => g!==id)">×</button>
                    </span>
                </template>
            </div>
            <div class="mt-4">
                <template x-for="actor in actors" :key="actor.id">
                    <div class="flex items-center border-b py-2 gap-4">
                        <div class="flex items-center gap-4 w-1/3 min-w-[200px]" x-data="{ zoomOpen: false }">
                            <img 
                                :src="actor.image || 'https://placehold.co/80x80?text=No+Img'" 
                                class="w-16 h-16 object-cover rounded cursor-pointer" 
                                @click="zoomOpen = true"
                            >
                            <a :href="actor.link" target="_blank" class="text-blue-600 font-semibold truncate" x-text="actor.name"></a>

                            <!-- Zoom Modal -->
                            <div 
                                x-show="zoomOpen" 
                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                            >
                                <img 
                                    :src="actor.image || 'https://placehold.co/80x80?text=No+Img'" 
                                    class="max-w-3xl max-h-[80vh] object-contain rounded shadow-lg"
                                    @click="zoomOpen = false"
                                >
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-1 w-1/3 min-w-[150px]">
                            <template x-for="genre in actor.genres" :key="genre.id">
                                <span class="text-sm bg-gray-200 px-2 py-1 rounded" x-text="genre.title"></span>
                            </template>
                        </div>
                        <div class="flex gap-2 justify-end w-1/3 min-w-[120px]">
                            <button 
                                @click="edit('actors', actor)" 
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full flex items-center justify-center"
                                title="Edit"
                            >
                                ✏️
                            </button>
                            <button 
                                @click="remove('actors', actor.id)" 
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full flex items-center justify-center"
                                title="Delete"
                            >
                                🗑️
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <!-- Sección Movies -->
        <div x-show="tab==='movies'">
            <h2 class="text-xl font-semibold mb-2">Movies</h2>
            <div class="mb-4 flex gap-2">
                <input type="text" x-model="movieSearch" placeholder="Title movie" class="border p-2 rounded flex-1">
                <input type="text" x-model="movieLink" placeholder="Link" class="border p-2 rounded flex-1">
                <select x-model="selectedGenre" @change="addGenre(selectedGenre)" class="border p-2 rounded w-full">
                    <option value="">Genre</option>
                    <template x-for="g in allGenres" :key="g.id">
                        <option :value="g.id" x-text="g.title"></option>
                    </template>
                </select>
                <div class="mt-2">
                    <input 
                        type="file" 
                        id="movieFile" 
                        @change="handleFileUpload($event)" 
                        class="hidden"
                    >
                    <label 
                        for="movieFile" 
                        class="bg-blue-500 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-600 transition"
                    >
                        ⬆️
                    </label>
                </div>
                <button @click="saveItem('movies')" class="bg-blue-500 text-white px-3 py-1 rounded">
                    Save
                </button>
            </div>
            <div class="flex flex-wrap gap-1 mb-2" x-show="allGenres.length">
                <template x-for="id in movieGenres" :key="id">
                    <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded flex items-center gap-1">
                        <span x-text="allGenres.find(g => g.id == id).title || 'No data'"></span>
                        <button @click="movieGenres = movieGenres.filter(g => g!==id)">×</button>
                    </span>
                </template>
            </div>
            <div class="mt-4">
                 <template x-for="movie in movies" :key="movie.id">
                    <div class="flex items-center border-b py-2 gap-4">
                        <div class="flex items-center gap-4 w-1/3 min-w-[200px]" x-data="{ zoomOpen: false }">
                            <img 
                                :src="movie.image || 'https://placehold.co/80x80?text=No+Img'" 
                                class="w-16 h-16 object-cover rounded cursor-pointer" 
                                @click="zoomOpen = true"
                            >
                            <a :href="movie.link" target="_blank" class="text-blue-600 font-semibold truncate" x-text="movie.title"></a>

                            <!-- Zoom Modal -->
                            <div 
                                x-show="zoomOpen" 
                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                            >
                                <img 
                                    :src="movie.image || 'https://placehold.co/80x80?text=No+Img'" 
                                    class="max-w-3xl max-h-[80vh] object-contain rounded shadow-lg"
                                    @click="zoomOpen = false"
                                >
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1 w-1/3 min-w-[150px]">
                            <template x-for="genre in movie.genres" :key="genre.id">
                                <span class="text-sm bg-gray-200 px-2 py-1 rounded" x-text="genre.title"></span>
                            </template>
                        </div>
                        <div class="flex gap-2 justify-end w-1/3 min-w-[120px]">
                            <button 
                                @click="edit('movies', movie)" 
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full flex items-center justify-center"
                                title="Edit"
                            >
                                ✏️
                            </button>
                            <button 
                                @click="remove('movies', movie.id)" 
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full flex items-center justify-center"
                                title="Delete"
                            >
                                🗑️
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Sección Links -->
        <div x-show="tab==='links'">
            <h2 class="text-xl font-semibold mb-2">Links</h2>
            <div class="mb-4 flex gap-2">
                <input type="text" x-model="linkSearch" placeholder="Title link" class="border p-2 rounded flex-1">
                <input type="text" x-model="linkUrl" placeholder="Link" class="border p-2 rounded flex-1">
                <select x-model="selectedGenre" @change="addGenre(selectedGenre)" class="border p-2 rounded w-full">
                    <option value="">Genre</option>
                    <template x-for="g in allGenres" :key="g.id">
                        <option :value="g.id" x-text="g.title"></option>
                    </template>
                </select>
                <div class="mt-2">
                    <input 
                        type="file" 
                        id="linkFile" 
                        @change="handleFileUpload($event)" 
                        class="hidden"
                    >
                    <label 
                        for="linkFile" 
                        class="bg-blue-500 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-600 transition"
                    >
                        ⬆️
                    </label>
                </div>
                <button @click="saveItem('links')" class="bg-blue-500 text-white px-3 py-1 rounded">
                    Save
                </button>
            </div>
            <div class="flex flex-wrap gap-1 mb-2" x-show="allGenres.length">
                <template x-for="id in linkGenres" :key="id">
                    <span class="bg-blue-200 text-blue-800 px-2 py-1 rounded flex items-center gap-1">
                        <span x-text="allGenres.find(g => g.id == id).title || 'No data'"></span>
                        <button @click="linkGenres = linkGenres.filter(g => g!==id)">×</button>
                    </span>
                </template>
            </div>
            <div class="mt-4">
                <template x-for="link in links" :key="link.id">
                    <div class="flex items-center border-b py-2 gap-4">
                        <div class="flex items-center gap-4 w-1/3 min-w-[200px]" x-data="{ zoomOpen: false }">
                            <img 
                                :src="link.image || 'https://placehold.co/80x80?text=No+Img'" 
                                class="w-16 h-16 object-cover rounded cursor-pointer" 
                                @click="zoomOpen = true"
                            >
                            <a :href="link.url" target="_blank" class="text-blue-600 font-semibold truncate" x-text="link.title"></a>

                            <!-- Zoom Modal -->
                            <div 
                                x-show="zoomOpen" 
                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
                            >
                                <img 
                                    :src="link.image || 'https://placehold.co/80x80?text=No+Img'" 
                                    class="max-w-3xl max-h-[80vh] object-contain rounded shadow-lg"
                                    @click="zoomOpen = false"
                                >
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1 w-1/3 min-w-[150px]">
                            <template x-for="genre in link.genres" :key="genre.id">
                                <span class="text-sm bg-gray-200 px-2 py-1 rounded" x-text="genre.title"></span>
                            </template>
                        </div>
                        <div class="flex gap-2 justify-end w-1/3 min-w-[120px]">
                            <button 
                                @click="edit('links', link)" 
                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full flex items-center justify-center"
                                title="Edit"
                            >
                                ✏️
                            </button>
                            <button 
                                @click="remove('links', link.id)" 
                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full flex items-center justify-center"
                                title="Delete"
                            >
                                🗑️
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</body>
</html>
