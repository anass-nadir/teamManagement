import request from '@/utils/request'

export function fetchPage(data) {
  return request({
    url: '/interview/get-all',
    method: 'get',
    params: { page: data.page, limit:data.limit, synthesis:data.synthesis,subject:data.subject, place:data.place, sort:data.sort  }
  })

}

export function fetchArticle(id) {
  return request({
    url: '/article/detail',
    method: 'get',
    params: { id }
  })
}

export function fetchPv(pv) {
  return request({
    url: '/article/pv',
    method: 'get',
    params: { pv }
  })
}

export function createArticle(data) {
  return request({
    url: '/article/create',
    method: 'post',
    data
  })
}

export function updateArticle(data) {
  return request({
    url: '/article/update',
    method: 'post',
    data
  })
}
