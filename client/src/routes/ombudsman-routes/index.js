import index from '@/components/ombudsman';
import Detail from '@/components/ombudsman/Detail.vue';
import Form from '@/components/ombudsman/Form.vue';

export default [
    {
        path: '/usuario/ouvidoria',
        name: 'ouvidoria',
        component: index,
        meta: { requiresAuth: true }
    },
    {
        path: '/usuario/ouvidoria/add',
        name: 'ouvidoria/add',
        component: Form,
        meta: { requiresAuth: true }
    },
    {
        path: '/usuario/ouvidoria/edit/:id',
        name: 'ouvidoria/edit',
        component: Form,
        meta: { requiresAuth: true }
    },
    {
        path: '/usuario/ouvidoria/detalhe/:id',
        name: 'ouvidoria/detalhe',
        component: Detail,
        meta: { requiresAuth: true }
    },
]
export const ombudsmanDemandsRoutes = require('./demans').default
export const ombudsmanOriginsRoutes = require('./origins').default
