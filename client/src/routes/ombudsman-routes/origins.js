import index from '@/components/ombudsman/origins';
import Form from '@/components/ombudsman/origins/Form.vue';

export default [
    {
        path: '/usuario/ouvidoria/origem',
        name: 'ouvidoria/origem',
        component: index,
        meta: { requiresAuth: true,  group: ['tecnologia-da-informacao-hu', 'servico-de-apoio-ao-cliente-hu', 'servico-apoio-ao-cliente-hpsc'] }
    },
    {
        path: '/usuario/ouvidoria/origem/add',
        name: 'ouvidoria/origem/add',
        component: Form,
        meta: { requiresAuth: true,  group: ['tecnologia-da-informacao-hu', 'servico-de-apoio-ao-cliente-hu', 'servico-apoio-ao-cliente-hpsc'] }
    },
    {
        path: '/usuario/ouvidoria/origem/edit/:id',
        name: 'ouvidoria/origem/edit',
        component: Form,
        meta: { requiresAuth: true,  group: ['tecnologia-da-informacao-hu', 'servico-de-apoio-ao-cliente-hu', 'servico-apoio-ao-cliente-hpsc'] }
    },
]