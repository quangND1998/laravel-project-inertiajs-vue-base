<template>
  <div>
    <portal-target name="dropdown" slim />
    <div style="overscroll-behavior: none; ">
      <!-- HEADING -->
      <div
        class="fixed w-full bg-blue-600 h-16 pt-2 text-white flex justify-between shadow-md"
        style="top:0px; overscroll-behavior: none;"
      >
        <!-- back button -->
        <a href="#">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            class="w-12 h-12 my-1 text-yellow-100 ml-2"
          >
            <path
              class="text-white fill-current"
              d="M9.41 11H17a1 1 0 0 1 0 2H9.41l2.3 2.3a1 1 0 1 1-1.42 1.4l-4-4a1 1 0 0 1 0-1.4l4-4a1 1 0 0 1 1.42 1.4L9.4 11z"
            />
          </svg>
        </a>
        <i class="fas fa-sync-alt w-12 mt-3 h-12 my-1 text-yellow-100 ml-2" @click="infiniteHandler"></i>
        <div class="my-3 text-white font-bold text-lg text-center">
        
          <span
            class="badge badge-success ml-2"
          >{{ user_online.length }} User online</span>
        </div>

        <!-- 3 dots -->
        <dropdown class="md:hidden" placement="bottom-end">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 h-8 mt-2 mr-2">
            <path
              class="text-white fill-current"
              fill-rule="evenodd"
              d="M12 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"
            />
          </svg>
          <div slot="dropdown">
            <ListUserOnline :usersOnline="user_online" />
          </div>
        </dropdown>
      </div>

      <!-- MESSAGES -->
      <div class="mt-20 mb-16 con">
        <!-- SINGLE MESSAGE -->
        <!-- SINGLE MESSAGE 2 -->
        <infinite-loading direction="top" @infinite="infiniteHandler" spinner="bubbles"></infinite-loading>
        <div v-for="(conversation,index) in conversations" :key="index" class="clearfix">
          <div class="mx-1 my-1 p-1" v-if="conversation.user.name !== $page.props.auth.user.name">
            <span class="primary-font">{{ conversation.user.name }}</span>
          </div>

          <div
            v-if="conversation.user.name !== $page.props.auth.user.name"
            class="w-3/4 mx-4 my-2 p-2 rounded-lg inline-block rounded-bl-none bg-gray-300 text-gray-600 clearfix"
          >
            {{ conversation.message }}
            <span
              class="text-gray-400 pr-1"
              style="font-size: 10px;"
            >{{ conversation.created_at }}</span>
          </div>

          <div
            v-else
            class="float-right w-3/4 mx-4 my-2 p-2 rounded-lg inline-block rounded-br-none bg-blue-600 text-white clearfix"
          >
            {{ conversation.message }}
            <span
              class="text-gray-400 mr-1"
              style="font-size: 10px;"
            >{{ conversation.created_at }}</span>
          </div>

          <span
            v-show="(index == Object.keys(conversations).length - 1) && (isSeen.length > 0) && (conversation.user.name == $page.props.auth.user.name)"
            class="text-gray-400 pr-1 float-right mx-4 p-2"
            style="font-size: 10px;"
          >
            <span v-for="(element,index) in isSeen" :key="index">{{ element.name }} seen</span>
          </span>
        </div>

        <span class="inline-block" v-if="UserTyping">
          <img src="/images/typing.gif" width="50px" height="250px" />
          {{ UserTyping.name }} is Typing ...
        </span>
      </div>
    </div>

    <!-- MESSAGE INPUT AREA -->
    <div class="fixed w-full flex justify-between text-black" style="bottom: 0px;">
      <textarea
        class="flex-grow m-2 py-2 px-4 mr-1 rounded-full border border-gray-300 bg-gray-200 resize-none"
        rows="1"
        placeholder="Message..."
        style="outline: none;"
        v-model="message"
        @click="seenMessage"
        @keydown="sendTypingEvent"
        @keyup.enter="store()"
      ></textarea>
      <button class="m-2" style="outline: none;" @click.prevent="store()">
        <svg
          class="svg-inline--fa text-blue-600 fa-paper-plane fa-w-16 w-12 h-12 py-2 mr-2"
          aria-hidden="true"
          focusable="false"
          data-prefix="fas"
          data-icon="paper-plane"
          role="img"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 512 512"
        >
          <path
            fill="currentColor"
            d="M476 3.2L12.5 270.6c-18.1 10.4-15.8 35.6 2.2 43.2L121 358.4l287.3-253.2c5.5-4.9 13.3 2.6 8.6 8.3L176 407v80.5c0 23.6 28.5 32.9 42.5 15.8L282 426l124.6 52.2c14.2 6 30.4-2.9 33-18.2l72-432C515 7.8 493.3-6.8 476 3.2z"
          />
        </svg>
      </button>
    </div>
  </div>
</template>

<script>
import ListUserOnline from "@/Pages/Chat/ListUserOnline"
import Dropdown from "@/Components/Dropdown";
export default {
  props: {
    user: Object
  },
  components: {
    Dropdown,
    ListUserOnline
  },
  data() {
    return {
      enabled: true,
      page: 1,
      conversations: [],
      message: "",
      user_online: [],
      UserTyping: false,
      isSeen: [],
      typingTimer: false
    };
  },
  mounted() {
    this.listenForNewMessage();
    // this.infiniteHandler();
  },
  methods: {
    store() {
      // console.log(this.message)
      if (this.message) {
        axios
          .post("/sendMessage", {
            message: this.message
          })
          .then(response => {
            console.log(response.errors)
            this.message = "";
            this.conversations.push(response.data);
            //  this.isSeem = false;
          });
      }

    },
    listenForNewMessage() {
        console.log('aaaaa')
      Echo.join("chat")
        .here((users) => {
          console.log('HERE ' + users);
          this.user_online = users

        })
        .joining((user) => {
          this.user_online.push(user);
        })
        .leaving((user) => {
          // this.isSeen =false
          this.user_online = this.user_online.filter(
            element => element.id != user.id
          )
        }).listen("MessageSend", (e) => {
          // console.log(e);
          this.conversations.push(e);
        }).listenForWhisper('typing', (user) => {
          this.UserTyping = user;
          if (this.typingTimer) {
            clearTimeout(this.typingTimer)
          }
          this.typingTimer = setTimeout(() => {
            this.UserTyping = false;
          }, 1000)
          // console.log(this.isSeen)
        }).listenForWhisper('seen', (user) => {
          // console.log('user', user)
          if (user == null) {
            this.isSeen = [];
          }
          else {
            this.isSeen.push(user);
            this.isSeen = [...new Map(this.isSeen.map(item => [item['id'], item])).values()];
          }
        });

      // Echo.private(`groups.${this.group.id}`).listen("NewMessage", (e) => {
      //   // console.log(e);
      //   this.conversations.push(e);
      // })
    },
    infiniteHandler($state) {
      axios
        .get("/fecthMessages", {
          params: {
            page: this.page
          }
        })
        .then(response => {
          if (response.data.data.length) {
            this.page += 1;
            this.conversations.unshift(...response.data.data.reverse());
            Bus.$emit("newMessenger", this.enabled);
            $state.loaded();

          } else {
            $state.complete();
          }
        });
    },
    sendTypingEvent() {
      Echo.join("chat")
        .whisper('typing', this.user);
    },
    seenMessage() {
      Echo.join("chat" )
        .whisper('seen', this.user);
    },
  }
};
</script>