import index from "@/components/collaborator";
import Form from "@/components/collaborator/Form.vue";

export default [
    {
        path: '/usuario/colaboradores',
        component: index,
        name: 'colaboradores',
        meta: { requiresAuth: true }
    },
    {
        path: '/usuario/colaboradores/add',
        component: Form,
        name: 'colaboradores/add',
        meta: { requiresAuth: true, groupAuth: ['tecnologia-da-informacao-hu', 'recursos-humanos-hu'] }
    },
    {
        path: '/usuario/colaboradores/edit/:id',
        component: Form,
        name: 'colaboradores/edit',
        meta: { requiresAuth: true, groupAuth: ['tecnologia-da-informacao-hu', 'recursos-humanos-hu'] }
    },
]