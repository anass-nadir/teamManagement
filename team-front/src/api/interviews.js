import request from '@/utils/request'

export function fetchPage(data) {
  return request({
    url: '/interview/get-all',
    method: 'get',
    params: { page: data.page, limit:data.limit, synthesis:data.synthesis,subject:data.subject, place:data.place, sort:data.sort  }
  })

}

export function getByMember(data) {
  return request({
    url: '/interview/get-by-member',
    method: 'get',
    params: { id:data.id, page: data.page, limit:data.limit, synthesis:data.synthesis,subject:data.subject, place:data.place, sort:data.sort  }
  })
}

export function draftInterview(interviewId) {
  return request({
    url: '/interview/trash',
    method: 'post',
    params: { interviewId }
  })
}
export function destroyInterview(interviewId) {
    return request({
      url: '/interview/destroy',
      method: 'post',
      params: { interviewId }
    })
  }

  export function publishInterview(interviewId) {
    return request({
      url: '/interview/publish',
      method: 'post',
      params: { interviewId }
    })
  }

  export function createInterview(data) {
    return request({
      url: '/interview/push-interview',
      method: 'post',
      data
    })
  }
  export function editInterview(data) {
    return request({
      url: '/interview/edit-interview',
      method: 'post',
      data
    })
  }
