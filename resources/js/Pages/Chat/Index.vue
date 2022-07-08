<template>
      <div>
    <div
    
      class="max-h-screen overflow-y-auto"
      v-chat-scroll="{always: true, smooth: true, scrollonremoved: true, smoothonremoved: true}"
    >
      <NewGroupChat  :user="user"></NewGroupChat>
    </div>
    </div>
</template>

<script>
import NewGroupChat from "./NewGroupChat";
export default {
    components: {
    NewGroupChat
  },
  props:{
    user:Object
  },
   data() {
    return {
      always: true,
      form: {}
    };
  },
}
</script>

<style>

</style>