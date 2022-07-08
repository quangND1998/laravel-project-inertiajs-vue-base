import ApiService from './api.service'

export const userService = {

    register(params) {
        // console.log('pageService',slug)
        return ApiService.post('users', params);
    },
    login(params) {
        console.log('pageservice', params)
        return ApiService.post("users/login", params)
    },

};