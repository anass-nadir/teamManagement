import axios from 'axios'
import { Message } from 'element-ui'
import store from '@/store'
import { getToken } from '@/utils/auth'
// create an axios instance
const service = axios.create({
  baseURL: process.env.BASE_API, // api 的 base_url
  timeout: 5000, // request timeout
  headers: {
    common: {        // can be common or any other method
        'X-Requested-With': 'XMLHttpRequest'
    }
  }
})

// request interceptor
service.interceptors.request.use(
  config => {
    // Do something before request is sent
    if (store.getters.token) {
      config.headers['Authorization'] = `Bearer ${getToken()}`
    }
    return config
  },
  error => {
    // Do something with request error
    console.log(error) // for debug
    Promise.reject(error)
  }
)

  let fetchedToken = false
  let subscribers = []

  const fetchToken = (access_token) => {
    subscribers = subscribers.filter(callback => callback(access_token))
  }

  const addSubscriber = (callback) => {
    subscribers.push(callback)
  }



// response interceptor
service.interceptors.response.use(
  response => response,

  error => {
    const { config, response: { status } } = error
    const originalRequest = config

    if (status === 401) {
        if (!fetchedToken) {
          fetchedToken = true
            store.dispatch('refreshToken').then((response) => {
            axios.defaults.headers.common['Authorization'] = `Bearer ${response.token}`
            fetchToken(response.token)
            fetchedToken = false
          }).catch((error) => {
            store.commit('logoutUser')
            return Promise.reject(error)
          })
        }
        const retryOriginalRequest = new Promise((resolve) => {
          addSubscriber(access_token => {
            originalRequest.headers.Authorization = 'Bearer ' + access_token
            resolve(axios(originalRequest))
          })
        })
        return retryOriginalRequest
      }

    console.log('err' + error) // for debug
    Message({
      message: error.message,
      type: 'error',
      duration: 5 * 1000
    })
    return Promise.reject(error)
  }
)

export default service
