import index from '@/components/room-training';
import Form from '@/components/room-training/Form.vue';

export default [
    {
        path: '/usuario/sala-treinamento',
        component: index,
        name: 'sala-treinamento',
        meta: { requiresAuth: true, group: ['recursos-humanos-hu', 'tecnologia-da-informacao-hu'] }
    },
    {
        path: '/usuario/sala-treinamento/add',
        component: Form,
        name: 'sala-treinamento/add',
        meta: { requiresAuth: true, group: ['recursos-humanos-hu', 'tecnologia-da-informacao-hu'] }
    },
    {
        path: '/usuario/sala-treinamento/edit/:id',
        component: Form,
        name: 'sala-treinamento/edit',
        meta: { requiresAuth: true, group: ['recursos-humanos-hu', 'tecnologia-da-informacao-hu'] }
    },
]