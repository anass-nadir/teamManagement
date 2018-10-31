import request from '@/utils/request'

export function loginByUsername(email, password) {
  const data = {
   "email":email,
   "password":password
  }
  return request({
    url: 'user/sign-in',
    method: 'post',
    data
  })
}

export function logout() {
  return request({
    url: 'user/logout',
    method: 'post'
  })
}

export function getUserData(token) {
  return request({
    url: 'user/getUserData',
    method: 'get',
    params: { token }
  })
}

