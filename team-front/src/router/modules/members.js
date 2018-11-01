
import Layout from '@/views/layout/Layout'

const members = {

        path: '/members',
        component: Layout,
        redirect: '/members',
        name:'members',
        meta: { title: 'Members', icon: 'peoples' },
        children: [
          {
            path: 'list',
            component: () => import('@/views/members/index'),
            name: 'list',
            meta: { title: 'List Members', icon: 'peoples', noCache: true }
          }
        ]


     /*  {

              path: '/interviews',
        component: Layout,
        redirect: 'interviews',
        meta: { title: 'Interviews', icon: 'documentations' },
        children: [
          {
            path: 'list',
            component: () => import('@/views/interviews/index'),
            name: 'list',
            meta: { noCache: false }
          }
        ]

        path: '/interviews',
        component: () => import('@/views/interviews/index'),
        name: 'interviews',
        meta: { title: 'interviews', icon: 'documentation', noCache: false }
      },
      {
        path: '/members',
        component: () => import('@/views/members/index'),
        name: 'members',
        meta: { title: 'Members', icon: 'peoples', noCache: true }
      }, */
}
export default members
