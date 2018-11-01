
import Layout from '@/views/layout/Layout'

const interviews = {

        path: '/interviews',
        component: Layout,
        redirect: '/interviews',
        name:'interviews',
        meta: { title: 'Interviews', icon: 'documentation' },
        children: [
          {
            path: 'list',
            component: () => import('@/views/interviews/index'),
            name: 'list',
            meta: { title: 'List Interviews', icon: 'documentation', noCache: true }
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
        path: '/interviews',
        component: () => import('@/views/interviews/index'),
        name: 'interviews',
        meta: { title: 'Members', icon: 'peoples', noCache: true }
      }, */
}
export default interviews
