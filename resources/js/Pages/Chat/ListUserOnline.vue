<template>
  <div class="card mb-sm-3 mb-md-0 contacts_card" aria-labelledby="dropdownButton">
    <div class="card-header">
      <h3 class="d-flex text-white">Online<span class="badge badge-success ml-2">{{ usersOnline.length }}</span></h3>
      <div class="input-group">
        <input v-model="searchQuery" type="text" placeholder="Search..." name="" class="form-control search">
        <div class="input-group-prepend">
          <span class="input-group-text search_btn"><i class="fas fa-search"></i></span>
        </div>
      </div>
    </div>
    <div class="card-body contacts_body">
      <div class="contacts">
        <li v-for="user in filteredUsersList" :key="user.id" >
          <div class="current-user-mark" v-if="user.id === $page.props.auth.user.id" />
          <div class="d-flex bd-highlight">
            <div class="img_cont">
             
              <span class="online_icon"></span>
            </div>
            <div class="user_info">
              <span>{{ user.name }} {{ user.id === $page.props.auth.user.id ? '(You)' : '' }}</span>
              <span
                class="badge badge-danger font-12px"
                v-if="user.new_messages"
              >
                {{ user.new_messages }}
              </span>
              <p>{{ user.name }} is online</p>
            </div>
          </div>
        </li>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    usersOnline: {
      type: Array,
      required: true
    }
  },
  data () {
    return {
      searchQuery: ''
    }
  },
  computed: {
    filteredUsersList () {
      return this.usersOnline.filter(row => row.name.toLowerCase().includes(this.searchQuery.toLowerCase()))
    }
  }
}
</script>

<style>
</style>