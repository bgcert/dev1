<template>
	<div>
		<div class="ui segment messanger">
			<div class="threads">

				<!-- Search -->
				<form class="ui form">
					<div class="field">
						<input type="text" name="first-name" placeholder="Намери потребител" v-model="searchInput" @focus="focus = true" @blur="focus = false">
					</div>
				</form>
				<div class="ui active centered inline small loader" v-show="loading"></div>

				<div class="ui middle aligned selection list" v-for="item in searchResults">
					<div class="item" @click.prevent="newUser(item)">
						<img class="ui avatar image" :src="item.picture">
						<div class="content">
							<div class="header">{{ item.full_name }}</div>
						</div>
					</div>
				</div>

				<!-- Thread List -->
					
				<ul  v-show="!focus && (searchInput == '')">
					<li v-for="thread in threads" @click.prevent="selectThread(thread)" :class="{ 'selected': thread.id == selectedThread, 'unread': thread.unread }">
						<router-link :to="'/t/' + thread.id">
							<div class="picture">
								<img :src="thread.first_contact.user.picture">
							</div>
							<div class="details">
								<p class="name">{{ thread.first_contact.user.full_name }}</p>
								<p v-if="thread.last_message">{{ thread.last_message.body }}</p>
							</div>
						</router-link>
					</li>
				</ul>
			</div>

			<!-- Messages Feed -->
			<div class="feed-container">
				<div class="ui divided items" v-if="contact">
					<div class="item">
						<div class="ui tiny circular image">
							<img :src="contact.picture">
						</div>
						<div class="middle aligned content">
							{{ contact.full_name }}
						</div>
					</div>
				</div>

				<div  class="messages-feed" ref="feed">
					<ul>
						<li v-for="message in messages" :class=" { 'message sent': message.sent == userId, 'message received': message.sent != userId }">
							<div class="text">
			                    {{ message.body }}
			                </div>
						</li>
					</ul>	
				</div>
				<textarea v-model="input" @keydown.enter.prevent="(contactIsNew) ? newMessage() : send()"></textarea>
			</div>
		</div>
	</div>
</template>

<script>
	import _ from 'lodash'; // Don't know why is not imported already.
	import { EventBus } from '../../app';
    export default {
    	data: function () {
    		return {
    			focus: false,
    			loading: false,
    			input: '',
    			searchInput: '',
    		}
    	},
    	computed: {
    		userId: function() {
    			return this.$store.getters.userId;
    		},
    		threads: function() {
    			return this.$store.getters.threads;
    		},
    		contact: function() {
    			return this.$store.getters.contact;
    		},
    		messages: function() {
    			return this.$store.getters.messages;
    		},
    		selectedThread: function() {
    			return this.$store.getters.selectedThread;
    		},
    		searchResults: function() {
    			return this.$store.getters.searchResults;
    		},
    		contactIsNew: function() {
    			return this.$store.getters.contactIsNew;
    		}
    	},
        methods: {
        	async newUser(item) {
        		this.searchQuery = '';
        		let exist = await this.$store.dispatch('existingContact', item.id);
				this.$store.commit('updateContact', item);
				this.$store.commit('updateMessages', []);
				this.$store.commit('updateSelectedThread', null);
				this.searchInput = '';
				// this.isNew = true;
        	},
        	selectThread(thread) {
        		this.$store.dispatch('selectThread', thread);
        	},
        	send() {
                if (this.input == '') return;
                this.$store.dispatch('sendMessage', this.input);
                this.input = '';
            },
            async newMessage() {
            	this.isNew = false;
            	await this.$store.dispatch('newContact');
            	this.send();
            },
            scrollToBottom() {
                setTimeout(() => {
                    this.$refs.feed.scrollTop = this.$refs.feed.scrollHeight - this.$refs.feed.clientHeight;
                }, 300);
            },
            searchAfterDebounce: _.debounce(
	            function () {
	                this.search()
	            }, 800 // 800 milliseconds
	        ),
    		search() {
    			this.$store.dispatch('search', this.searchInput);
    		},
    		async load() {
    			await this.$store.dispatch('load');
    		}
        },
        watch: {
            selected: function () {
                this.scrollToBottom();
            },
            messages: function () {
                this.scrollToBottom();
            },
            searchInput: function (val) {
	        	if (val.length > 2) {
	        		this.loading = true;
	        		this.searchAfterDebounce();
	        	} else {
	        		this.loading = false;
	        		this.$store.commit('clearSearchResults');
	        	}
	        }
        },
        mounted() {
            console.log('Messanger App Component mounted.');
            this.$store.dispatch('load', this.$route.params.id);
            // if (this.$route.query.contact) {
            // 	this.$store.dispatch('load', this.$route.query.contact);
            // 	this.isNew = true;
            // }
        },
        created() {
        	
        }
    };
</script>

<style lang="scss" scoped>
	.unread {
		border: 3px solid red;
	}
	.messanger {
		display: flex;
		max-width: 1300px;
		margin: 0 auto;
		height: 700px;
		.threads {
		    flex: 2;
		    overflow-y: scroll;
		    border-right: 1px solid #a6a6a6;
		    
		    ul {
		        list-style-type: none;
		        padding-left: 0;
		        li {
		        	&.selected {
		                background: #e9ebee;
		            }
		        }
		        li a {
		            display: flex;
		            padding: 2px;
		            border-bottom: 1px solid #f1f1f1;
		            height: 80px;
		            position: relative;
		            cursor: pointer;
		            .picture {
		                flex: 1;
		                display: flex;
		                align-items: center;
		                img {
		                    width: 35px;
		                    border-radius: 50%;
		                    margin: 0 auto;
		                }
		            }
		            .details {
		                flex: 3;
		                font-size: 14px;
		                overflow: hidden;
		                display: flex;
		                flex-direction: column;
		                justify-content: center;
		                p {
		                    margin: 0;
		                    &.name {
		                        font-weight: bold;
		                    }
		                }
		            }
		        }
		    }
		}
		.feed-container {
			flex: 5;
		}
		.ui.divided.items {
			border-bottom: 1px solid #e9ebee;
			margin: 0;
			padding: 10px;
		}
		textarea {
		    width: 96%;
		    margin: 10px;
		    resize: none;
		    border-radius: 3px;
		    border: 1px solid lightgray;
		    padding: 6px;
		}
		.messages-feed {
		    height: 100%;
		    max-height: 520px;
		    overflow-y: scroll;
		    ul {
		        list-style-type: none;
		        padding: 5px;
		        li {
		            &.message {
		                margin: 10px 0;
		                width: 100%;
		                .text {
		                    max-width: 200px;
		                    border-radius: 5px;
		                    padding: 12px;
		                    display: inline-block;
		                }
		                &.received {
		                    text-align: right;
		                    .text {
		                        background: #b2b2b2;
		                        color: #fff;
		                    }
		                }
		                &.sent {
		                    text-align: left;
		                    .text {
		                        background: #81c4f9;
		                    }
		                }
		            }
		        }
		    }
		}
	}
	::-webkit-scrollbar {
	    width: 0px;  /* remove scrollbar space */
	    background: transparent;  /* optional: just make scrollbar invisible */
	}
</style>