
import Layout from '@/views/layout/Layout'

const interviews = {

        path: '/interviews',
        component: Layout,
        redirect: '/interviews/list',
        name:'interviews',
        meta: { title: 'interviews', icon: 'documentation' },
        children: [
          {
            path: 'list',
            component: () => import('@/views/interviews/index'),
            name: 'list-interviews',
            meta: { title: 'interviewsList', icon: 'documentation', noCache: true }
          }
        ]

}
export default interviews
