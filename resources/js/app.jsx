import Alpine from 'alpinejs';
import axios from 'axios';

window.Alpine = Alpine;

Alpine.data('app', () => ({
    tab: 'actors',
    actors: [],
    movies: [],
    links: [],
    actorSearch: '',
    actorLink: '',
    actorImage: '',
    movieSearch: '',
    movieLink: '',
    movieImage: '',
    linkSearch: '',
    linkImage: '',
    linkUrl: '',
    errorMessage: '',
    editMode: false,
    editType: '',
    editItem: null,
    allGenres: [],
    actorGenres: [],
    movieGenres: [],
    linkGenres: [],
    selectedGenre: null,

    async init() {
        const resGenres = await axios.get('/api/genres');
        this.allGenres = resGenres.data;
        await this.cargarDatos();
        this.$watch('actorSearch', async (value) => {
            const q = value.trim();
            if (q === '') {
                await this.cargarDatos(); // Si está vacío, recarga todo
            } else {
                await this.search('actors'); // Si tiene texto, busca
            }
        });
        this.$watch('movieSearch', async (value) => {
            const q = value.trim();
            if (q === '') {
                await this.cargarDatos();
            } else {
                await this.search('movies');
            }
        });
        this.$watch('linkSearch', async (value) => {
            const q = value.trim();
            if (q === '') {
                await this.cargarDatos();
            } else {
                await this.search('links');
            }
        });
        this.$watch('selectedGenre', async (value) => {
            if (!value) {
                await this.cargarDatos();
            } else {
                await this.filterByGenre(this.tab);
            }
        });
    },

    async cargarDatos() {
        try {
            const resActors = await axios.get('/api/actors');
            const resMovies = await axios.get('/api/movies');
            const resLinks = await axios.get('/api/links');

            this.actors = resActors.data;
            this.movies = resMovies.data;
            this.links = resLinks.data;
            this.errorMessage = '';
        } catch (e) {
            console.error(e);
            this.errorMessage = '⚠️ Error loading';
            reloadAfterDelay();
        }
    },

    async remove(type, id) {
        try {
            if (!confirm('Sure to delete?')) return;
            await axios.delete(`/api/${type}/${id}`);
            await this.cargarDatos();
            this.errorMessage = '';
        } catch (e) {
            console.error(e);
            this.errorMessage = '⚠️ Error deleting';
        }
    },

    async reloadAfterDelay() {
        try {
            // Show a temporary message
            this.errorMessage = '⏳ Reloading in 3 seconds...';

            // Wait 3 seconds
            await new Promise(resolve => setTimeout(resolve, 3000));

            // Reload data
            await this.cargarDatos();

            // Clear message
            this.errorMessage = '';
        } catch (e) {
            console.error(e);
            this.errorMessage = '⚠️ Error reloading data';
        }
    },


    async edit(type, item) {
        this.editMode = true;
        this.editType = type;
        this.editItem = { ...item }; 
        this.actorSearch = '';
        this.actorLink = '';
        this.actorImage = '';
        this.actorGenres = [];
        this.movieSearch = '';
        this.movieLink = '';
        this.movieImage = '';
        this.movieGenres = [];
        this.linkSearch = '';
        this.linkUrl = '';
        this.linkImage = '';
        this.linkGenres = [];

        if (type === 'actors') {
            this.actorSearch = item.name;
            this.actorLink = item.link;
            this.actorImage = item.image ?? '';
            this.actorGenres = item.genres.map(g => g.id);
        } else if (type === 'movies') {
            this.movieSearch = item.title;
            this.movieLink = item.link;
            this.movieImage = item.image ?? '';
            this.movieGenres = item.genres.map(g => g.id); 
        } else if (type === 'links') {
            this.linkSearch = item.title; 
            this.linkUrl = item.url || item.link || '';
            this.linkImage = item.image ?? '';
            this.linkGenres = item.genres.map(g => g.id);
        }
    },


    addGenre(id) {
        if (!id) return;
        id = Number(id);

        // Determine which array to update based on the active tab
        let targetArray;
        if (this.tab === 'actors') targetArray = this.actorGenres;
        else if (this.tab === 'movies') targetArray = this.movieGenres;
        else if (this.tab === 'links') targetArray = this.linkGenres;
        else return;

        // Add if it doesn't exist yet
        if (!targetArray.includes(id)) {
            targetArray.push(id);
        }

        // Reset the select
        this.selectedGenre = null;
    },

    async saveItem(type) {
        try {
            let payload = {};
            let clearFields = [];

            switch(type) {
                case 'actors':
                    payload = {
                        name: this.actorSearch.trim(),
                        link: this.actorLink.trim(),
                        image: this.actorImage.trim(),
                        genre_ids: this.actorGenres.map(Number)
                    };
                    clearFields = ['actorSearch','actorLink','actorImage','actorGenres'];
                    break;

                case 'movies':
                    payload = {
                        title: this.movieSearch.trim(),
                        link: this.movieLink.trim(),
                        image: this.movieImage.trim(),
                        genre_ids: this.movieGenres.map(Number)
                    };
                    clearFields = ['movieSearch','movieLink','movieImage','movieGenres'];
                    break;

                case 'links':
                    payload = {
                        title: this.linkSearch?.trim(),
                        url: this.linkUrl?.trim(),
                        image: this.linkImage.trim(),
                        genre_ids: this.linkGenres.map(Number)
                    };
                    clearFields = ['linkSearch','linkUrl','linkGenres', 'linkImage'];
                    break;

                default:
                    return;
            }

            // If editing, use PUT
            if(this.editMode && this.editItem && this.editType === type) {
                await axios.put(`/api/${type}/${this.editItem.id}`, payload);
            } else { // Else, create new
                await axios.post(`/api/${type}`, payload);
            }

            // Clear fields
            clearFields.forEach(f => this[f] = Array.isArray(this[f]) ? [] : '');

            this.editMode = false;
            this.editType = '';
            this.editItem = null;

            await this.cargarDatos();
            this.errorMessage = '';
        } catch (e) {
            console.error(e);
            this.errorMessage = '⚠️ Error saving';
        }
    },

    handleFileUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            if (this.tab === 'actors') this.actorImage = e.target.result;
            else if (this.tab === 'movies') this.movieImage = e.target.result;
            else if (this.tab === 'links') this.linkImage = e.target.result;
        };
        reader.readAsDataURL(file);
    },

    handlePaste(event) {
        const items = event.clipboardData.items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const file = items[i].getAsFile();
                const reader = new FileReader();
                reader.onload = (e) => {
                    if (this.tab === 'actors') this.actorImage = e.target.result;
                    else if (this.tab === 'movies') this.movieImage = e.target.result;
                    else if (this.tab === 'links') this.linkImage = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    },

    async search(type, genreId = null) {
        let query = '';
        let url = '';

        switch (type) {
            case 'actors':
                query = this.actorSearch.trim();
                url = '/api/actors';
                break;
            case 'movies':
                query = this.movieSearch.trim();
                url = '/api/movies';
                break;
            case 'links':
                query = this.linkSearch.trim();
                url = '/api/links';
                break;
            default:
                console.error('Unsupported type:', type);
                return;
        }

        // Build query params dynamically
        const params = new URLSearchParams();
        if (query) params.append('q', query);
        if (genreId) params.append('genre', genreId);

        try {
            const res = await axios.get(`${url}?${params.toString()}`);

            switch (type) {
                case 'actors':
                    this.actors = res.data;
                    break;
                case 'movies':
                    this.movies = res.data;
                    break;
                case 'links':
                    this.links = res.data;
                    break;
            }

            this.errorMessage = '';
        } catch (e) {
            console.error(e);
            this.errorMessage = `⚠️ Error searching ${type}`;
        }
    },

    async filterByGenre(type) {
        const genreId = this.selectedGenre;

        if (!genreId) {
            await this.cargarDatos(); // Reload all if no genre selected
            return;
        }

        // Perform search by genre
        await this.search(type, genreId);
    }
}));

Alpine.start();
