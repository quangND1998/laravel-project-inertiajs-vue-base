<template>
  <!-- <div class="page-not-found">
    <div class="wrapper not-found">
      <h1 class="animated fadeIn">{{title}}</h1>
      <div class="desc animated fadeIn">
        <span>OOPS!</span>
        <br />
        {{ description }}
      </div>
      <Link :href="route('dashboard')" class="btn btn-primary btn-back-home mt-4 animated fadeInUp">
        <span class="btn-label mr-2">
          <i class="flaticon-home"></i>
        </span>
        Back To Home
      </Link>
    </div>
  </div>-->
  <div
    class="flex items-center justify-center w-screen h-screen bg-gradient-to-r from-indigo-800 to-blue-200"
  >
    <div class="px-40 py-20 bg-white rounded-md shadow-xl">
      <div class="flex flex-col items-center">
        <h1 class="font-bold text-blue-600 text-9xl">{{ title }}</h1>

        <h6 class="mb-2 text-2xl font-bold text-center text-gray-800 md:text-3xl">
          <span class="text-red-500">Oops!</span>
          {{ description }}
        </h6>

        <!-- <Link href="/forum" class="px-6 py-2 text-sm rounded-md font-semibold text-blue-800 bg-blue-100">Go home</Link> -->
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue";
export default {
  components: {
    Link
  },
  props: {
    status: Number
  },
  computed: {
    title() {
      return {
        503: "503",
        500: "500",
        404: "404",
        403: "403",
        429: "429"
      }[this.status];
    },
    description() {
      return {
        503: "Sorry, we are doing some maintenance. Please check back soon.",
        500: "Whoops, something went wrong on our servers.",
        404: "Sorry, the page you are looking for could not be found.",
        403: "Sorry, you are forbidden from accessing this page.",
        429: "Too many request in serve"
      }[this.status];
    }
  }
};
</script>