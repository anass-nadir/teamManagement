
import Layout from '@/views/layout/Layout'

const members = {

        path: '/members',
        component: Layout,
        redirect: '/members/list',
        name:'members',
        meta: { title: 'members', icon: 'peoples', roles:['admin'] },
        children: [
          {
            path: 'list',
            component: () => import('@/views/members/index'),
            name: 'list-members',
            meta: { title: 'membersList', icon: 'peoples', noCache: true }
          }
        ]

}
export default members
