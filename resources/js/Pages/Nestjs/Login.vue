<template>
  <div>
    <breeze-validation-errors class="mb-4" />

    <!-- <div v-if="status" class="mb-4 font-medium text-sm text-green-600">{{ status }}</div> -->

    <form @submit.prevent="submit">
      <div class="text-center mb-3">
        <img class="image-logo" src="asset/img/HolomiaExpo.svg" alt />
      </div>
      <h1 class="text-center">Login</h1>
      <div>
        <breeze-label for="user_name" value="UserName" />
        <input
          id="user_name"
          type="text"
          class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
          v-model="username"
          required
          autofocus
        />
      </div>
      <div>
        <breeze-label for="email" value="Email" />
        <input
          id="email"
          type="email"
          class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
          v-model="email"
          required
          autofocus
          autocomplete="username"
        />
      </div>

      <div class="mt-4">
        <breeze-label for="password" value="Password" />
        <div class="password-hidden">
          <input
            id="password"
            :type="passwordFieldType"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 block w-full"
            v-model="password"
            required
            autocomplete="current-password"
          />
          <span class="span-hidden">
            <i
              :class="passwordFieldType =='password'? 'fa fa-eye' :'fa fa-eye-slash'"
              @click="switchVisibility"
            ></i>
          </span>
        </div>
      </div>

      <div class="flex items-center justify-end mt-4">
        <breeze-button class="ml-4">Log in</breeze-button>
      </div>
      <div class="col-md-12" style="padding-left:65px">
        <!-- <a class="btn" :href="'/auth/facebook'">
          <i class="fab fa-facebook fa-2x" aria-hidden="true"></i>
        </a>-->
        <!-- <a class="btn" :href="'auth/google'">
          <i class="fab fa-google-plus-square fa-2x" aria-hidden="true"></i>
        </a>-->
      </div>
    </form>
  </div>
</template>

<script>
import BreezeButton from "@/Components/Button";
import BreezeGuestLayout from "@/Layouts/Guest";
import BreezeInput from "@/Components/Input";
import BreezeCheckbox from "@/Components/Checkbox";
import BreezeLabel from "@/Components/Label";
import BreezeValidationErrors from "@/Components/ValidationErrors";
import { Link } from "@inertiajs/inertia-vue";
import jwtToken from "../../token";
import { userService } from "../../common/UserService.js";
import { nullLiteral } from "@babel/types";
export default {
  layout: BreezeGuestLayout,

  components: {
    BreezeButton,
    BreezeInput,
    BreezeCheckbox,
    BreezeLabel,
    BreezeValidationErrors,
    Link
  },

  props: {
    canResetPassword: Boolean,
    status: String
  },

  data() {
    return {
      passwordFieldType: "password",
      username: null,
      email: null,
      password: null
    };
  },

  methods: {
    submit() {
      userService
        .login({
          username: this.username,
          email: this.email,
          password: this.password
        })
          .then(response => {
            console.log(response)
          jwtToken.saveToken(response.user.token);
        })
          .catch(error => {
           
          throw new Error(error);
        });
    },
    switchVisibility() {
      this.passwordFieldType =
        this.passwordFieldType === "password" ? "text" : "password";
    }
  }
};
</script>
<style scoped>
.show-password {
  position: absolute;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 22px;
  cursor: pointer;
}

.form-floating-label {
  position: relative;
}
.password-hidden {
  position: relative;
}
.span-hidden {
  top: 30%;
  position: absolute;
  right: 20px;
  transition: auto;
}
</style>
