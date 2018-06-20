export default {
    state: {
    	userId: null,
    	threads: [],
    	messages: [],
    	contact: [],
    	selectedThread: null,
    	searchResults: []
    },

    getters: {
    	userId(state) { return state.userId; },
        threads(state) { return state.threads; },
        contact(state) { return state.contact; },
        messages(state) { return state.messages; },
        selectedThread(state) { return state.selectedThread; },
        searchResults(state) { return state.searchResults; },
    },

    mutations: {
    	updateThreads(state, payload) {
    		state.threads = payload[0];
    		state.userId = payload[1];
    	},

    	updateMessages(state, payload) {
    		state.messages = payload;
    	},

    	updateSelectedThread(state, payload) {
    		state.selectedThread = payload;
    	},

    	updateContact(state, payload) {
    		state.contact = payload;
    	},

    	updateSearchResults(state, payload) {
    		state.searchResults = payload;
    	},

    	unshiftThread(state, payload) {
    		state.threads.unshift(payload);
    	},

    	pushMessage(state, payload) {
    		state.messages.push(payload);
    	},

    	// updateSeen(state, payload) {
    	// 	console.log(payload);
    	// 	console.log('seen');
    	// },

    	clearSearchResults(state) {
    		state.searchResults = [];
    	}
    },

    actions: {
    	async load(context) {
    		await context.dispatch('getThreads');

    		context.dispatch('listen');
    		if (context.getters.threads.length == 0) return;
    		await context.dispatch('selectThread', context.getters.threads[0]);
    		context.dispatch('getMessages', context.getters.selectedThread);
    	},

    	getThreads(context) {
    		return new Promise((resolve, reject) => {
	    		axios.get('messages/threads')
	        		.then((response) => {
	        			context.commit('updateThreads', response.data);
	        			resolve()
	        		})
	        		.catch((error) => {
	        			reject(error);
	        		})
    		})
        },

        getMessages(context, id) {
        	return new Promise((resolve, reject) => {
	        	let route = 'messages/thread/' + id;
	        	axios.get(route)
	        		.then((response) => {
	        			context.commit('updateMessages', response.data.messages);
	        			resolve()
	        		});
        	})
        },

        selectThread(context, thread) {
        	return new Promise((resolve, reject) => {
	        	context.commit('updateContact', thread.first_participant.user);
	        	context.commit('updateSelectedThread', thread.id);
	        	context.dispatch('getMessages', thread.id);
	        	resolve();
        	})
        },

        listen(context) {
        	Echo.private('messages.' + context.getters.userId)
                	.listen('NewMessage', (e) => {
                		if (e.message.thread_id == context.getters.selectedThread) {
                			context.commit('pushMessage', e.message);
                		}
                });

            Echo.private('threads.' + context.getters.userId)
                	.listen('NewThread', (e) => {
                		let route = 'messages/thread/' + e.thread_id;
			        	axios.get(route)
			        		.then((response) => {
			        			response.data.unread = true;
			        			context.commit('unshiftThread', response.data);
			        		});
                });
        },

        async newContact(context) {
        	let id = await context.dispatch('newThread');
        	context.commit('updateSelectedThread', id);
        },

        newThread(context) {
        	return new Promise((resolve, reject) => {
	        	axios.post('messages/new', {
		                	to: context.getters.contact.id,
		                }).then((response) => {
		                	context.commit('unshiftThread', response.data);
		                	resolve(response.data.id)
		                });
	        })
        },

        sendMessage(context, message) {
        	axios.post('messages/add', {
                	thread_id: context.getters.selectedThread,
                    input: message
                }).then((response) => {
                	context.commit('pushMessage', response.data);
                });
        },

        seen(context) {
        	axios.post('messages/seen', {
                	thread_id: context.getters.selectedThread,
                }).then((response) => {
                	context.commit('updateSeen', response.data);
                });
        },

        search(context, input) {
	        	axios.post('messages/user/search', { searchQuery: input })
	        		.then(function (response) {
	        			context.commit('updateSearchResults', response.data);
					})
					.catch(function (error) {
						console.log(error);
					});
		}
    }
}